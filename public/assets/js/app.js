/**
 * POS Application - Main JavaScript
 * Core utilities and helpers
 */

(function () {
    'use strict';

    // ========================================
    // CSRF Token Helper
    // ========================================
    const CSRF = {
        getToken: function () {
            const tokenEl = document.querySelector('meta[name="csrf-token"]');
            return tokenEl ? tokenEl.getAttribute('content') : '';
        },

        getHeaderName: function () {
            return 'X-CSRF-Token';
        }
    };

    // ========================================
    // Toast Notification System
    // ========================================
    function showToast(message, type) {
        type = type || 'info';

        // Create container if it doesn't exist
        let container = document.querySelector('.toast-container');
        if (!container) {
            container = document.createElement('div');
            container.className = 'toast-container';
            document.body.appendChild(container);
        }

        // Create toast element
        const toast = document.createElement('div');
        toast.className = 'toast toast-' + type;

        // Icon mapping
        var icons = {
            success: '&#10004;',
            error: '&#10008;',
            warning: '&#9888;',
            info: '&#8505;'
        };

        // Title mapping
        var titles = {
            success: 'Success',
            error: 'Error',
            warning: 'Warning',
            info: 'Info'
        };

        toast.innerHTML =
            '<span class="toast-icon">' + (icons[type] || icons.info) + '</span>' +
            '<div class="toast-content">' +
                '<div class="toast-title">' + (titles[type] || titles.info) + '</div>' +
                '<div class="toast-message">' + message + '</div>' +
            '</div>' +
            '<button class="toast-close" aria-label="Close">&times;</button>';

        container.appendChild(toast);

        // Close button handler
        var closeBtn = toast.querySelector('.toast-close');
        closeBtn.addEventListener('click', function () {
            removeToast(toast);
        });

        // Auto-remove after 5 seconds
        setTimeout(function () {
            removeToast(toast);
        }, 5000);
    }

    function removeToast(toast) {
        if (!toast || toast.classList.contains('removing')) return;
        toast.classList.add('removing');
        setTimeout(function () {
            if (toast.parentNode) {
                toast.parentNode.removeChild(toast);
            }
        }, 300);
    }

    // ========================================
    // Confirmation Dialog
    // ========================================
    function confirmAction(message) {
        message = message || 'Are you sure you want to perform this action?';
        return confirm(message);
    }

    // ========================================
    // AJAX Helper
    // ========================================
    function ajaxRequest(url, method, data) {
        method = method || 'GET';
        data = data || null;

        var headers = {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        };

        // Add CSRF token for state-changing requests
        if (['POST', 'PUT', 'PATCH', 'DELETE'].indexOf(method.toUpperCase()) !== -1) {
            headers[CSRF.getHeaderName()] = CSRF.getToken();
        }

        var options = {
            method: method,
            headers: headers,
            credentials: 'same-origin'
        };

        if (data && ['POST', 'PUT', 'PATCH'].indexOf(method.toUpperCase()) !== -1) {
            options.body = JSON.stringify(data);
        }

        return fetch(url, options)
            .then(function (response) {
                return response.text().then(function (text) {
                    try {
                        return JSON.parse(text);
                    } catch (e) {
                        return text;
                    }
                }).then(function (result) {
                    if (!response.ok) {
                        var message = typeof result === 'object' && result.message
                            ? result.message
                            : 'Request failed with status ' + response.status;
                        return Promise.reject(new Error(message));
                    }
                    return result;
                });
            });
    }

    // Convenience methods
    var ajax = {
        get: function (url) {
            return ajaxRequest(url, 'GET');
        },

        post: function (url, data) {
            return ajaxRequest(url, 'POST', data);
        },

        put: function (url, data) {
            return ajaxRequest(url, 'PUT', data);
        },

        patch: function (url, data) {
            return ajaxRequest(url, 'PATCH', data);
        },

        delete: function (url) {
            return ajaxRequest(url, 'DELETE');
        }
    };

    // ========================================
    // Form Validation Helper
    // ========================================
    var FormValidator = {
        rules: {},
        errors: {},

        init: function (formElement) {
            this.form = formElement;
            this.errors = {};
            return this;
        },

        addRule: function (fieldName, rules) {
            this.rules[fieldName] = rules;
            return this;
        },

        validate: function () {
            var self = this;
            this.errors = {};

            Object.keys(this.rules).forEach(function (fieldName) {
                var field = self.form.querySelector('[name="' + fieldName + '"]');
                var value = field ? field.value.trim() : '';
                var fieldRules = self.rules[fieldName];

                fieldRules.forEach(function (rule) {
                    var error = self._checkRule(rule, fieldName, value);
                    if (error) {
                        if (!self.errors[fieldName]) {
                            self.errors[fieldName] = [];
                        }
                        self.errors[fieldName].push(error);

                        // Mark field as invalid
                        if (field) {
                            field.classList.add('is-invalid');
                            field.classList.remove('is-valid');
                        }
                    }
                });

                // Mark field as valid if no errors
                if (!self.errors[fieldName] && field) {
                    field.classList.remove('is-invalid');
                    if (value !== '') {
                        field.classList.add('is-valid');
                    }
                }
            });

            return Object.keys(this.errors).length === 0;
        },

        _checkRule: function (rule, fieldName, value) {
            if (typeof rule === 'string') {
                switch (rule) {
                    case 'required':
                        if (!value) return 'This field is required';
                        break;
                    case 'email':
                        if (value && !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(value)) {
                            return 'Please enter a valid email address';
                        }
                        break;
                    case 'number':
                        if (value && isNaN(value)) return 'Please enter a valid number';
                        break;
                }
            } else if (typeof rule === 'object') {
                if (rule.required && !value) return rule.message || 'This field is required';
                if (rule.min && value.length < rule.min) {
                    return rule.message || 'Minimum ' + rule.min + ' characters required';
                }
                if (rule.max && value.length > rule.max) {
                    return rule.message || 'Maximum ' + rule.max + ' characters allowed';
                }
                if (rule.pattern && !rule.pattern.test(value)) {
                    return rule.message || 'Invalid format';
                }
                if (rule.custom && typeof rule.custom === 'function') {
                    return rule.custom(value);
                }
            }
            return null;
        },

        getErrors: function () {
            return this.errors;
        },

        hasErrors: function () {
            return Object.keys(this.errors).length > 0;
        },

        clearValidation: function () {
            var self = this;
            if (this.form) {
                this.form.querySelectorAll('.is-invalid, .is-valid').forEach(function (field) {
                    field.classList.remove('is-invalid', 'is-valid');
                });
            }
            this.errors = {};
        }
    };

    // ========================================
    // Loading Overlay
    // ========================================
    var Loading = {
        show: function (message) {
            var overlay = document.querySelector('.loading-overlay');
            if (!overlay) {
                overlay = document.createElement('div');
                overlay.className = 'loading-overlay';
                overlay.innerHTML = '<div class="spinner spinner-lg"></div>';
                if (message) {
                    overlay.innerHTML += '<p style="margin-top: 1rem; color: var(--gray-600);">' + message + '</p>';
                }
                document.body.appendChild(overlay);
            }
            overlay.classList.remove('hidden');
        },

        hide: function () {
            var overlay = document.querySelector('.loading-overlay');
            if (overlay) {
                overlay.classList.add('hidden');
                setTimeout(function () {
                    if (overlay.parentNode) {
                        overlay.parentNode.removeChild(overlay);
                    }
                }, 300);
            }
        }
    };

    // ========================================
    // Currency Formatter
    // ========================================
    function formatCurrency(amount, currency) {
        currency = currency || 'Rp';
        var number = parseFloat(amount);
        if (isNaN(number)) return currency + ' 0';
        return currency + ' ' + number.toLocaleString('id-ID', {
            minimumFractionDigits: 0,
            maximumFractionDigits: 2
        });
    }

    // ========================================
    // Sidebar Toggle
    // ========================================
    function initSidebar() {
        var toggle = document.querySelector('.sidebar-toggle');
        var sidebar = document.querySelector('.sidebar');

        if (toggle && sidebar) {
            toggle.addEventListener('click', function () {
                if (window.innerWidth <= 767) {
                    sidebar.classList.toggle('mobile-open');
                } else {
                    sidebar.classList.toggle('collapsed');
                }
            });

            // Close sidebar on mobile when clicking outside
            document.addEventListener('click', function (e) {
                if (window.innerWidth <= 767 &&
                    !sidebar.contains(e.target) &&
                    !toggle.contains(e.target) &&
                    sidebar.classList.contains('mobile-open')) {
                    sidebar.classList.remove('mobile-open');
                }
            });
        }
    }

    // ========================================
    // Form Submit with AJAX
    // ========================================
    function initAjaxForms() {
        document.addEventListener('submit', function (e) {
            var form = e.target;
            if (!form.hasAttribute('data-ajax')) return;

            e.preventDefault();

            var validator = Object.create(FormValidator);
            validator.init(form);

            // Collect validation rules from data attributes
            form.querySelectorAll('[data-validate]').forEach(function (field) {
                var rules = field.getAttribute('data-validate').split('|');
                validator.addRule(field.name, rules);
            });

            if (!validator.validate()) {
                showToast('Please fix the errors in the form', 'error');
                return;
            }

            var formData = new FormData(form);
            var data = {};
            formData.forEach(function (value, key) {
                data[key] = value;
            });

            var url = form.getAttribute('action') || window.location.href;
            var method = form.getAttribute('method') || 'POST';

            Loading.show();

            ajaxRequest(url, method, data)
                .then(function (response) {
                    showToast(response.message || 'Operation successful', 'success');

                    if (response.redirect) {
                        window.location.href = response.redirect;
                    } else if (response.reload) {
                        window.location.reload();
                    }
                })
                .catch(function (error) {
                    showToast(error.message || 'An error occurred', 'error');
                })
                .finally(function () {
                    Loading.hide();
                });
        });
    }

    // ========================================
    // Delete Confirmation
    // ========================================
    function initDeleteButtons() {
        document.addEventListener('click', function (e) {
            var deleteBtn = e.target.closest('[data-delete]');
            if (!deleteBtn) return;

            e.preventDefault();

            var message = deleteBtn.getAttribute('data-delete') || 'Are you sure you want to delete this item?';

            if (!confirmAction(message)) return;

            var url = deleteBtn.getAttribute('href') || deleteBtn.getAttribute('data-url');
            if (!url) return;

            Loading.show();

            ajaxRequest(url, 'DELETE')
                .then(function (response) {
                    showToast(response.message || 'Item deleted successfully', 'success');
                    if (response.reload || true) {
                        window.location.reload();
                    }
                })
                .catch(function (error) {
                    showToast(error.message || 'Failed to delete item', 'error');
                })
                .finally(function () {
                    Loading.hide();
                });
        });
    }

    // ========================================
    // Document Ready Initialization
    // ========================================
    function onReady(callback) {
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', callback);
        } else {
            callback();
        }
    }

    // Initialize all components
    onReady(function () {
        initSidebar();
        initAjaxForms();
        initDeleteButtons();
        initViewToggle();
    });

    // ========================================
    // View Toggle (Grid/List)
    // ========================================
    function initViewToggle() {
        var toggles = document.querySelectorAll('.view-toggle-btn');
        for (var i = 0; i < toggles.length; i++) {
            toggles[i].addEventListener('click', function() {
                var view = this.getAttribute('data-view');
                var container = document.getElementById(this.getAttribute('data-container') || 'viewContainer');
                if (!container) return;

                // Update active button in same group
                var siblings = this.parentElement.querySelectorAll('.view-toggle-btn');
                for (var j = 0; j < siblings.length; j++) siblings[j].classList.remove('active');
                this.classList.add('active');

                // Switch view
                if (view === 'grid') {
                    container.classList.add('view-grid');
                    container.classList.remove('view-list');
                } else {
                    container.classList.remove('view-grid');
                    container.classList.add('view-list');
                }

                localStorage.setItem('pos-view-mode', view);
            });
        }

        // Restore saved preference
        var saved = localStorage.getItem('pos-view-mode');
        if (saved) {
            var btn = document.querySelector('.view-toggle-btn[data-view="' + saved + '"]');
            if (btn) btn.click();
        }
    }

    // ========================================
    // Expose to global scope
    // ========================================
    window.showToast = showToast;
    window.confirmAction = confirmAction;
    window.ajaxRequest = ajaxRequest;
    window.ajax = ajax;
    window.FormValidator = FormValidator;
    window.Loading = Loading;
    window.formatCurrency = formatCurrency;
    window.CSRF = CSRF;

})();
