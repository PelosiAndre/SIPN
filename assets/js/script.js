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
        modal.classList.toggle('modal-visible');
        document.body.style.overflow = modal.classList.contains('modal-visible') ? 'hidden' : '';
    };

    triggers.forEach(trigger => {
        if (trigger) trigger.addEventListener('click', toggleModal);
    });

    const closeBtn = document.getElementById('btn-close-modal');
    if (closeBtn) closeBtn.addEventListener('click', toggleModal);

    modal.addEventListener('click', (e) => {
        if (e.target === modal) toggleModal();
    });
});