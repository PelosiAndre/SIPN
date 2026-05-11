document.addEventListener('DOMContentLoaded', () => {
    const modal = document.getElementById('login-modal');
    const triggers = [
        document.getElementById('btn-login-trigger'),
        document.getElementById('btn-hero-login'),
        document.getElementById('btn-feature-login'),
        document.getElementById('btn-footer-login')
    ];

    const toggleModal = (e) => {
        if (e) e.preventDefault();
        if(modal) {
            modal.classList.toggle('modal-visible');
            document.body.style.overflow = modal.classList.contains('modal-visible') ? 'hidden' : '';
        }
    };

    triggers.forEach(trigger => {
        if (trigger) trigger.addEventListener('click', toggleModal);
    });

    const closeBtn = document.getElementById('btn-close-modal');
    if (closeBtn) closeBtn.addEventListener('click', toggleModal);

    if(modal) {
        modal.addEventListener('click', (e) => {
            if (e.target === modal) toggleModal();
        });
    }

    const btnTabLogin = document.getElementById('btn-tab-login');
    const btnTabRegister = document.getElementById('btn-tab-register');
    const sectionLogin = document.getElementById('section-login');
    const sectionRegister = document.getElementById('section-register');
    const sectionForgotRequest = document.getElementById('section-forgot-request');
    const btnShowForgot = document.getElementById('btn-show-forgot');
    const btnsBackLogin = document.querySelectorAll('.btn-back-login');
    const authToggleHeader = document.getElementById('auth-toggle-header');

    if (btnTabLogin && btnTabRegister) {
        btnTabLogin.addEventListener('click', (e) => {
            e.preventDefault();
            btnTabLogin.classList.add('active');
            btnTabRegister.classList.remove('active');
            sectionLogin.classList.remove('hidden');
            sectionRegister.classList.add('hidden');
            if(sectionForgotRequest) sectionForgotRequest.classList.add('hidden');
        });

        btnTabRegister.addEventListener('click', (e) => {
            e.preventDefault();
            btnTabRegister.classList.add('active');
            btnTabLogin.classList.remove('active');
            sectionRegister.classList.remove('hidden');
            sectionLogin.classList.add('hidden');
            if(sectionForgotRequest) sectionForgotRequest.classList.add('hidden');
            
            const formFunc = document.getElementById('form-register-func');
            if (formFunc && !formFunc.dataset.logged) {
                const cod = formFunc.getAttribute('data-codigo');
                console.log(`[SISTEMA] Código de autorização para novos funcionários: ${cod}`);
                formFunc.dataset.logged = 'true';
            }
        });
    }

    if (btnShowForgot && sectionForgotRequest) {
        btnShowForgot.addEventListener('click', () => {
            sectionLogin.classList.add('hidden');
            sectionForgotRequest.classList.remove('hidden');
            if(authToggleHeader) authToggleHeader.classList.add('hidden');
        });
    }

    btnsBackLogin.forEach(btn => {
        btn.addEventListener('click', () => {
            if(sectionForgotRequest) sectionForgotRequest.classList.add('hidden');
            sectionLogin.classList.remove('hidden');
            if(authToggleHeader) authToggleHeader.classList.remove('hidden');
        });
    });

    const sidebarLinks = document.querySelectorAll('.sidebar-link');
    sidebarLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            sidebarLinks.forEach(l => l.classList.remove('active'));
            this.classList.add('active');
        });
    });

    const searchCatalog = document.getElementById('search-catalog');
    if (searchCatalog) {
        searchCatalog.addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase();
            const catalogItems = document.querySelectorAll('.catalog-item');

            catalogItems.forEach(item => {
                const title = item.querySelector('.course-title').textContent.toLowerCase();
                const entity = item.querySelector('.course-entity').textContent.toLowerCase();
                
                if (title.includes(searchTerm) || entity.includes(searchTerm)) {
                    item.style.display = 'flex';
                } else {
                    item.style.display = 'none';
                }
            });
        });
    }
});