document.addEventListener('DOMContentLoaded', function() {

    /* ============ MOBILE NAV ============ */

    var hamburger = document.getElementById('hamburger');
    var mobileNav = document.getElementById('mobileNav');

    if (hamburger && mobileNav) {
        hamburger.addEventListener('click', function() {
            var isOpen = mobileNav.classList.toggle('is-open');
            hamburger.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
        });
    }

    /* ============ DROPDOWNS ============ */
    document.querySelectorAll('.has-dropdown > .dropdown-toggle').forEach(function(button) {
        button.addEventListener('click', function() {
            var parent = button.closest('.has-dropdown');
            var isOpen = parent.classList.toggle('is-open');
            button.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
            var dropdown = parent.querySelector('.dropdown');
            if (dropdown) {
                dropdown.style.opacity = isOpen ? '1' : '';
                dropdown.style.visibility = isOpen ? 'visible' : '';
                dropdown.style.transform = isOpen ? 'translateY(0)' : '';
            }
        });
    });

    /* =========================================================
       COMMITTEE CAROUSEL
       ========================================================= */
    var carousel = document.querySelector('.carousel');

    if (carousel) {
        var committees = [];
        try {
            committees = JSON.parse(carousel.dataset.committees || '[]');
        } catch (error) {
            committees = [];
        }

        var track = carousel.querySelector('.carousel-track');
        var prevBtn = carousel.querySelector('.carousel-arrow--prev');
        var nextBtn = carousel.querySelector('.carousel-arrow--next');
        var titleEl = document.getElementById('carouselTitle');
        var descEl = document.getElementById('carouselDesc');

        var activeIndex = 0;
        var isAnimating = false;
        var animationTime = 400;

        /* =====================================================
           FORCED CAROUSEL STRUCTURAL CSS
           ===================================================== */
        var style = document.createElement('style');
        style.textContent = `
            .carousel {
                overflow: hidden !important;
                position: relative !important;
                width: 100% !important;
                display: flex !important;
                align-items: center !important;
                justify-content: center !important;
                gap: 18px !important;
                touch-action: pan-y;
            }

            .carousel-track {
                display: flex !important;
                align-items: center !important;
                justify-content: center !important;
                position: relative !important;
                width: 100% !important;
                transform: translateX(0px);
                transition: transform ${animationTime}ms cubic-bezier(.22,.61,.36,1);
            }

            .carousel-track [data-role] {
                transition: opacity ${animationTime}ms ease, transform ${animationTime}ms ease, filter ${animationTime}ms ease;
                will-change: transform, opacity;
                flex-shrink: 0 !important;
            }

            .carousel-track [data-role="prev"],
            .carousel-track [data-role="next"] {
                opacity: 0.4;
                transform: scale(0.85);
                cursor: pointer;
            }

            .carousel-track [data-role="center"] {
                opacity: 1;
                transform: scale(1);
                z-index: 2;
            }

            .carousel-arrow {
                cursor: pointer;
                transition: transform 0.2s ease, opacity 0.2s ease;
                z-index: 10;
            }
            .carousel-arrow:hover { transform: scale(1.08); }
            .carousel-arrow:active { transform: scale(0.92); }
            .carousel-track.is-dragging { transition: none !important; }
        `;
        document.head.appendChild(style);

        function getIndex(index) {
            if (!committees.length) return 0;
            return (index + committees.length) % committees.length;
        }

        function getSlide(role) {
            if (!track) return null;
            return track.querySelector('[data-role="' + role + '"]');
        }

        function updateSlide(role, index) {
            var slide = getSlide(role);
            if (!slide || !committees.length) return;
            var item = committees[getIndex(index)];
            var image = slide.querySelector('img');
            var label = slide.querySelector('.carousel-slide-label');

            if (image) {
                image.src = item.image;
                image.alt = item.title;
            }
            if (label) {
                label.textContent = item.title;
            }
            slide.dataset.categoryKey = item.key;
        }

        function updateText() {
            if (!committees.length) return;
            var current = committees[activeIndex];
            if (titleEl) titleEl.textContent = current.title;
            if (descEl) descEl.textContent = current.desc;
            var moreBtn = document.getElementById('carouselMoreBtn') || document.querySelector('.carousel-more');
            if (moreBtn && current && current.key) {
                moreBtn.href = 'committee.php?type=' + encodeURIComponent(current.key);
            }
        }

        function renderCarousel() {
            if (!committees.length || !track) return;
            updateSlide('prev', activeIndex - 1);
            updateSlide('center', activeIndex);
            updateSlide('next', activeIndex + 1);
            updateText();
        }

        function goNext() {
            if (isAnimating || committees.length <= 1) return;
            isAnimating = true;

            // Smooth track translation shift simulation left
            track.style.transform = 'translateX(-100px)';

            setTimeout(function() {
                activeIndex = getIndex(activeIndex + 1);
                track.style.transition = 'none';
                track.style.transform = 'translateX(0px)';

                updateSlide('prev', activeIndex - 1);
                updateSlide('center', activeIndex);
                updateSlide('next', activeIndex + 1);
                updateText();

                void track.offsetWidth;
                track.style.transition = 'transform ' + animationTime + 'ms cubic-bezier(.22,.61,.36,1)';
                isAnimating = false;
            }, animationTime);
        }

        function goPrev() {
            if (isAnimating || committees.length <= 1) return;
            isAnimating = true;

            // Smooth track translation shift simulation right
            track.style.transform = 'translateX(100px)';

            setTimeout(function() {
                activeIndex = getIndex(activeIndex - 1);
                track.style.transition = 'none';
                track.style.transform = 'translateX(0px)';

                updateSlide('prev', activeIndex - 1);
                updateSlide('center', activeIndex);
                updateSlide('next', activeIndex + 1);
                updateText();

                void track.offsetWidth;
                track.style.transition = 'transform ' + animationTime + 'ms cubic-bezier(.22,.61,.36,1)';
                isAnimating = false;
            }, animationTime);
        }

        if (prevBtn) {
            prevBtn.addEventListener('click', function(event) {
                event.preventDefault();
                event.stopPropagation();
                goPrev();
            });
        }

        if (nextBtn) {
            nextBtn.addEventListener('click', function(event) {
                event.preventDefault();
                event.stopPropagation();
                goNext();
            });
        }

        var previousSlide = getSlide('prev');
        var followingSlide = getSlide('next');

        if (previousSlide) {
            previousSlide.addEventListener('click', function(event) {
                event.preventDefault();
                if (!isAnimating) goPrev();
            });
        }

        if (followingSlide) {
            followingSlide.addEventListener('click', function(event) {
                event.preventDefault();
                if (!isAnimating) goNext();
            });
        }

        var centerSlide = getSlide('center');
        if (centerSlide) {
            centerSlide.style.cursor = 'pointer';
            centerSlide.setAttribute('title', 'Click to view committee openings');
            centerSlide.addEventListener('click', function() {
                var current = committees[activeIndex];
                if (current && current.key) {
                    window.location.href = 'committee.php?type=' + encodeURIComponent(current.key);
                }
            });
        }

        // Initialize
        renderCarousel();
    }

    /* =========================================================
       FILTER POSTINGS & UTILITIES
       ========================================================= */
    function filterPostingsByCategory(categoryKey) {
        document.querySelectorAll('.posting-card').forEach(function(card) {
            card.style.display = card.dataset.category === categoryKey ? '' : 'none';
        });
    }

    document.querySelectorAll('[data-category-link]').forEach(function(link) {
        link.addEventListener('click', function(event) {
            event.preventDefault();
            var key = link.dataset.categoryLink;
            filterPostingsByCategory(key);
            var section = document.getElementById('committees');
            if (section) {
                section.scrollIntoView({ behavior: 'smooth' });
            }
        });
    });

    /* =========================================================
       REGISTRATION ROLE TOGGLE
       ========================================================= */
    var roleButtons = document.querySelectorAll('[data-role-btn]');
    var roleInput = document.getElementById('register-role');
    if (roleButtons.length && roleInput) {
        roleButtons.forEach(function(btn) {
            btn.addEventListener('click', function() {
                var selectedRole = btn.dataset.roleBtn;
                roleButtons.forEach(function(b) { b.classList.remove('is-active'); });
                btn.classList.add('is-active');
                roleInput.value = selectedRole;

                document.querySelectorAll('[data-role-field]').forEach(function(field) {
                    if (field.dataset.roleField === selectedRole) {
                        field.hidden = false;
                    } else {
                        field.hidden = true;
                    }
                });
            });
        });
    }

    /* =========================================================
       PASSWORD VISIBILITY TOGGLE
       ========================================================= */
    document.querySelectorAll('.password-toggle').forEach(function(toggle) {
        toggle.addEventListener('click', function() {
            var targetId = toggle.dataset.toggleFor;
            var input = document.getElementById(targetId);
            if (!input) return;
            var isPassword = input.type === 'password';
            input.type = isPassword ? 'text' : 'password';
            toggle.setAttribute('aria-pressed', isPassword ? 'true' : 'false');
        });
    });

    /* =========================================================
       LIVE CHAT WIDGET (contact.php)
       ========================================================= */
    var chatForm = document.getElementById('chat-form');
    var chatMessages = document.getElementById('chat-messages');
    var chatMessageInput = document.getElementById('chat-message-input');
    var chatNameInput = document.getElementById('chat-name-input');

    if (chatForm && chatMessages) {
        function renderMessages(messages) {
            if (!messages || !messages.length) {
                chatMessages.innerHTML = '<p class="chat-empty">Say hello — we\'re happy to help.</p>';
                return;
            }
            chatMessages.innerHTML = messages.map(function(msg) {
                return '<div class="chat-bubble chat-bubble--' + (msg.sender === 'client' ? 'client' : 'admin') + '">' +
                       '<p>' + escapeHtml(msg.message) + '</p>' +
                       '<span>' + escapeHtml(msg.time) + '</span>' +
                       '</div>';
            }).join('');
            chatMessages.scrollTop = chatMessages.scrollHeight;
        }

        function escapeHtml(str) {
            var div = document.createElement('div');
            div.textContent = str || '';
            return div.innerHTML;
        }

        function pollChat() {
            fetch('chat.php?action=poll')
                .then(function(res) { return res.json(); })
                .then(function(data) {
                    if (data.success && data.messages) {
                        renderMessages(data.messages);
                    }
                })
                .catch(function() {});
        }

        chatForm.addEventListener('submit', function(e) {
            e.preventDefault();
            var msg = (chatMessageInput ? chatMessageInput.value : '').trim();
            var name = (chatNameInput ? chatNameInput.value : '').trim();
            if (!msg) return;

            var formData = new FormData();
            formData.append('action', 'send');
            formData.append('message', msg);
            if (name) formData.append('name', name);

            fetch('chat.php', { method: 'POST', body: formData })
                .then(function(res) { return res.json(); })
                .then(function(data) {
                    if (data.success) {
                        chatMessageInput.value = '';
                        pollChat();
                    }
                })
                .catch(function() {});
        });

        pollChat();
        setInterval(pollChat, 4000);
    }

    /* =========================================================
       APPLICATION AJAX ACTIONS
       ========================================================= */
    document.addEventListener('submit', function(e) {
        var form = e.target.closest('[data-application-form]');
        if (!form) return;
        e.preventDefault();

        var formData = new FormData(form);
        fetch('applications.php', { method: 'POST', body: formData })
            .then(function(res) { return res.json(); })
            .then(function(data) {
                if (data.requiresLogin) {
                    window.location.href = 'login.php';
                    return;
                }
                if (data.postingHtml && form.parentElement) {
                    form.parentElement.innerHTML = data.postingHtml;
                } else if (data.message) {
                    alert(data.message);
                    location.reload();
                }
            })
            .catch(function() {
                form.submit();
            });
    });

});