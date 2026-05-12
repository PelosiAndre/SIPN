document.addEventListener('DOMContentLoaded', () => {
    const modal = document.getElementById('login-modal');
    const deleteModal = document.getElementById('delete-modal');
    
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

    const toggleDeleteModal = (e) => {
        if (e) e.preventDefault();
        if(deleteModal) {
            deleteModal.classList.toggle('modal-visible');
            document.body.style.overflow = deleteModal.classList.contains('modal-visible') ? 'hidden' : '';
            
            if(!deleteModal.classList.contains('modal-visible')) {
                document.getElementById('codigo-auth').value = '';
            }
        }
    };

    triggers.forEach(trigger => {
        if (trigger) trigger.addEventListener('click', toggleModal);
    });

    const closeBtn = document.getElementById('btn-close-modal');
    if (closeBtn) closeBtn.addEventListener('click', toggleModal);

    const closeDeleteBtn = document.getElementById('btn-close-delete-modal');
    if (closeDeleteBtn) closeDeleteBtn.addEventListener('click', toggleDeleteModal);

    if(modal) {
        modal.addEventListener('click', (e) => {
            if (e.target === modal) toggleModal();
        });
    }

    if(deleteModal) {
        deleteModal.addEventListener('click', (e) => {
            if (e.target === deleteModal) toggleDeleteModal();
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

    const lessonItems = document.querySelectorAll('.lesson-item');
    lessonItems.forEach(item => {
        item.addEventListener('click', function() {
            const lessonTitle = this.querySelector('.lesson-title').textContent;
            const h2 = document.querySelector('.lesson-info h2');
            if(h2) h2.textContent = `Aula atual: ${lessonTitle}`;
        });
    });

    const downloadBtns = document.querySelectorAll('.btn-download-cert');
    downloadBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            const targetId = this.getAttribute('data-target');
            const targetCard = document.getElementById(targetId);
            
            const printWrapper = document.createElement('div');
            printWrapper.classList.add('print-wrapper');
            
            const clone = targetCard.cloneNode(true);
            clone.classList.add('printing');
            
            printWrapper.appendChild(clone);
            document.body.appendChild(printWrapper);
            
            document.body.classList.add('print-mode');
            
            window.print();
            
            setTimeout(() => {
                document.body.classList.remove('print-mode');
                printWrapper.remove();
            }, 500);
        });
    });

    const deleteTriggers = document.querySelectorAll('.btn-delete-trigger');
    deleteTriggers.forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            const type = this.getAttribute('data-type');
            const id = this.getAttribute('data-id');
            
            document.getElementById('delete-tipo').value = type;
            document.getElementById('delete-id').value = id;
            
            toggleDeleteModal();
        });
    });

    const btnAddLesson = document.getElementById('btn-add-lesson');
    const lessonsWrapper = document.getElementById('lessons-wrapper');

    if (btnAddLesson && lessonsWrapper) {
        btnAddLesson.addEventListener('click', () => {
            const newRow = document.createElement('div');
            newRow.classList.add('lesson-row');
            newRow.innerHTML = `
                <fieldset class="input-group">
                    <label>Título da Aula</label>
                    <input type="text" name="aula_titulo[]" required>
                </fieldset>
                <fieldset class="input-group">
                    <label>Duração</label>
                    <input type="text" name="aula_duracao[]" placeholder="Ex: 15:30" required>
                </fieldset>
                <fieldset class="input-group">
                    <label>URL do Vídeo</label>
                    <input type="url" name="aula_video[]" placeholder="https://..." required>
                </fieldset>
                <button type="button" class="btn-remove-lesson">X</button>
            `;
            lessonsWrapper.appendChild(newRow);
            updateRemoveButtons();
        });

        lessonsWrapper.addEventListener('click', (e) => {
            if (e.target.classList.contains('btn-remove-lesson')) {
                const row = e.target.closest('.lesson-row');
                if(lessonsWrapper.children.length > 1) {
                    row.remove();
                    updateRemoveButtons();
                }
            }
        });

        function updateRemoveButtons() {
            const removeBtns = lessonsWrapper.querySelectorAll('.btn-remove-lesson');
            if (removeBtns.length === 1) {
                removeBtns[0].disabled = true;
            } else {
                removeBtns.forEach(btn => btn.disabled = false);
            }
        }
        
        updateRemoveButtons();
    }
});