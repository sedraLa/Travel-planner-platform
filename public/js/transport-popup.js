document.addEventListener('DOMContentLoaded', () => {
    const popupOverlay = document.getElementById('popup-overlay');
    const popupForm = document.getElementById('popup-form');
    const popupTitle = document.getElementById('popup-title');
    const closePopupBtn = document.getElementById('close-popup');

    const nameInput = document.getElementById('popup-name');
    const typeSelect = document.getElementById('popup-type');
    const descTextarea = document.getElementById('popup-description');

    // Add Service
    const addServiceBtn = document.getElementById('popup-btn');
    addServiceBtn?.addEventListener('click', () => {
        popupOverlay.style.display = 'flex';
        popupTitle.textContent = 'Add New Service';
        popupForm.action = popupForm.dataset.addAction;
        nameInput.value = '';
        typeSelect.value = 'Airport pick up';
        descTextarea.value = '';
    });

    // Edit Service
    document.querySelectorAll('.edit-btn').forEach(btn => {
        btn.addEventListener('click', () => {
            popupOverlay.style.display = 'flex';
            popupTitle.textContent = 'Edit Service';
            const id = btn.dataset.id;
            popupForm.action = `/transport/${id}`;

            nameInput.value = btn.dataset.name;
            descTextarea.value = btn.dataset.description;

            const typeValue = btn.dataset.type.trim().toLowerCase();
            Array.from(typeSelect.options).forEach(option => {
                option.selected = (option.value.trim().toLowerCase() === typeValue);
            });
        });
    });

    // Close Popup
    closePopupBtn?.addEventListener('click', () => popupOverlay.style.display = 'none');
    window.addEventListener('click', e => {
        if (e.target === popupOverlay) popupOverlay.style.display = 'none';
    });
});
