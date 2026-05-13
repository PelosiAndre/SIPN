var player;

function onYouTubeIframeAPIReady() {
    const frame = document.getElementById('lesson-player');
    if (!frame) return;

    player = new YT.Player('lesson-player', {
        events: {
            'onStateChange': onPlayerStateChange
        }
    });
}

function onPlayerStateChange(event) {
    if (event.data == YT.PlayerState.ENDED) {
        const form = document.querySelector('.lesson-action-form');
        if (form) {
            form.submit();
        }
    }
}

document.addEventListener('DOMContentLoaded', () => {
    const modal = document.getElementById('login-modal');
    const deleteModal = document.getElementById('delete-modal');
    
    const editModalAluno = document.getElementById('edit-modal-aluno');
    const editModalFuncionario = document.getElementById('edit-modal-funcionario');
    const editModalCurso = document.getElementById('edit-modal-curso');
    const editModalEntidade = document.getElementById('edit-modal-entidade');
    
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
                const input = document.getElementById('codigo-auth');
                if(input) input.value = '';
            }
        }
    };

    const toggleEditModal = (modalElement) => {
        if(modalElement) {
            modalElement.classList.toggle('modal-visible');
            document.body.style.overflow = modalElement.classList.contains('modal-visible') ? 'hidden' : '';
        }
    };

    triggers.forEach(trigger => {
        if(trigger) trigger.addEventListener('click', toggleModal);
    });

    const btnCloseModal = document.getElementById('btn-close-modal');
    if(btnCloseModal) btnCloseModal.addEventListener('click', toggleModal);

    const btnTabLogin = document.getElementById('btn-tab-login');
    const btnTabRegister = document.getElementById('btn-tab-register');
    const secLogin = document.getElementById('section-login');
    const secRegister = document.getElementById('section-register');
    const toggleHeader = document.getElementById('auth-toggle-header');

    if(btnTabLogin && btnTabRegister) {
        btnTabLogin.addEventListener('click', () => {
            btnTabLogin.classList.add('active');
            btnTabRegister.classList.remove('active');
            secLogin.classList.remove('hidden');
            secRegister.classList.add('hidden');
        });

        btnTabRegister.addEventListener('click', () => {
            btnTabRegister.classList.add('active');
            btnTabLogin.classList.remove('active');
            secRegister.classList.remove('hidden');
            secLogin.classList.add('hidden');
        });
    }

    const btnShowForgot = document.getElementById('btn-show-forgot');
    const btnBackLogin = document.querySelectorAll('.btn-back-login');
    const secForgotReq = document.getElementById('section-forgot-request');

    if(btnShowForgot) {
        btnShowForgot.addEventListener('click', () => {
            secLogin.classList.add('hidden');
            if(toggleHeader) toggleHeader.classList.add('hidden');
            secForgotReq.classList.remove('hidden');
        });
    }

    btnBackLogin.forEach(btn => {
        btn.addEventListener('click', () => {
            secForgotReq.classList.add('hidden');
            const secReset = document.getElementById('section-forgot-reset');
            if (secReset) secReset.classList.add('hidden');
            if(toggleHeader) toggleHeader.classList.remove('hidden');
            secLogin.classList.remove('hidden');
        });
    });

    const btnDownloadCerts = document.querySelectorAll('.btn-download-cert');
    if (btnDownloadCerts.length > 0) {
        btnDownloadCerts.forEach(btn => {
            btn.addEventListener('click', () => {
                const targetId = btn.getAttribute('data-target');
                const certElement = document.getElementById(targetId);
                
                if (certElement) {
                    const printWrapper = document.createElement('div');
                    printWrapper.className = 'print-wrapper';
                    
                    const certClone = certElement.cloneNode(true);
                    certClone.classList.add('printing');
                    
                    printWrapper.appendChild(certClone);
                    document.body.appendChild(printWrapper);
                    
                    document.body.classList.add('print-mode');
                    
                    window.print();
                    
                    setTimeout(() => {
                        document.body.classList.remove('print-mode');
                        printWrapper.remove();
                    }, 500);
                }
            });
        });
    }

    function setupLessonDynamic(wrapperId, btnId) {
        const wrapper = document.getElementById(wrapperId);
        const btn = document.getElementById(btnId);

        if (wrapper && btn) {
            btn.addEventListener('click', () => {
                const newRow = document.createElement('article');
                newRow.className = 'lesson-row-dynamic';
                newRow.innerHTML = `
                    <fieldset class="input-group input-group-flex">
                        <label>URL do Vídeo</label>
                        <input type="url" name="aula_video[]" placeholder="https://www.youtube.com/watch?v=..." required>
                    </fieldset>
                    <button type="button" class="btn-remove-lesson btn-outline-small">X</button>
                `;
                wrapper.appendChild(newRow);
                updateRemoveButtons(wrapper);
            });

            wrapper.addEventListener('click', (e) => {
                if (e.target.classList.contains('btn-remove-lesson')) {
                    const row = e.target.closest('.lesson-row-dynamic');
                    if(wrapper.children.length > 1) {
                        row.remove();
                        updateRemoveButtons(wrapper);
                    }
                }
            });

            updateRemoveButtons(wrapper);
        }
    }

    function updateRemoveButtons(wrapper) {
        const removeBtns = wrapper.querySelectorAll('.btn-remove-lesson');
        if (removeBtns.length === 1) {
            removeBtns[0].disabled = true;
        } else {
            removeBtns.forEach(btn => btn.disabled = false);
        }
    }

    setupLessonDynamic('lessons-wrapper', 'btn-add-lesson');
    setupLessonDynamic('edit-lessons-wrapper', 'btn-add-edit-lesson');
});