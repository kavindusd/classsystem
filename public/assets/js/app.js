// ClassSystem — Premium UI Interactions

document.addEventListener('DOMContentLoaded', () => {
    initPremiumSelects();
});

/**
 * Transforms standard selects with .form-select class into premium custom dropdowns
 */
function initPremiumSelects() {
    const selects = document.querySelectorAll('select.form-select');
    
    selects.forEach(select => {
        // Skip if already initialized
        if (select.dataset.premiumInit) return;
        select.dataset.premiumInit = 'true';
        
        const container = document.createElement('div');
        container.className = 'premium-select-container relative w-full md:w-auto';
        
        const trigger = document.createElement('div');
        trigger.className = 'form-select premium-trigger flex items-center justify-between cursor-pointer';
        trigger.tabIndex = 0;
        
        const selectedText = document.createElement('span');
        selectedText.className = 'truncate pr-4';
        selectedText.innerText = select.options[select.selectedIndex]?.text || 'Select...';
        
        trigger.appendChild(selectedText);
        // Chevron is handled by CSS background-image on .form-select if we keep the class, 
        // but we might want a custom one that rotates.
        
        const menu = document.createElement('div');
        menu.className = 'premium-select-menu absolute top-full left-0 mt-2 min-w-full bg-white border border-gray-200 rounded-xl shadow-xl z-[100] opacity-0 translate-y-[-10px] pointer-events-none transition-all duration-200 backdrop-blur-xl bg-white/90 overflow-hidden';
        
        const list = document.createElement('div');
        list.className = 'max-h-60 overflow-y-auto py-1';
        
        Array.from(select.options).forEach((option, index) => {
            const item = document.createElement('div');
            item.className = `premium-select-item px-4 py-2.5 text-sm font-bold cursor-pointer transition-colors flex items-center justify-between group ${option.selected ? 'active' : 'text-gray-700'}`;
            item.innerHTML = `
                <span>${option.text}</span>
                ${option.selected ? '<i class="fa-solid fa-check text-[10px]"></i>' : ''}
            `;
            
            item.addEventListener('click', (e) => {
                e.stopPropagation();
                select.selectedIndex = index;
                select.dispatchEvent(new Event('change'));
                
                // Update UI
                selectedText.innerText = option.text;
                container.querySelectorAll('.premium-select-item').forEach(i => {
                    i.classList.remove('active');
                    i.classList.add('text-gray-700');
                    const check = i.querySelector('i');
                    if (check) check.remove();
                });
                item.classList.add('active');
                item.classList.remove('text-gray-700');
                item.insertAdjacentHTML('beforeend', '<i class="fa-solid fa-check text-[10px]"></i>');
                
                closeMenu();
            });
            
            list.appendChild(item);
        });
        
        menu.appendChild(list);
        container.appendChild(trigger);
        container.appendChild(menu);
        
        // Hide original select but keep it in DOM for form submission
        select.style.display = 'none';
        select.parentNode.insertBefore(container, select);
        container.appendChild(select); // Move select inside container to keep it together
        
        const openMenu = () => {
            menu.classList.remove('opacity-0', 'translate-y-[-10px]', 'pointer-events-none');
            menu.classList.add('opacity-100', 'translate-y-0', 'pointer-events-auto');
            trigger.classList.add('ring-2', 'ring-emerald-500/20', 'border-emerald-500');
        };
        
        const closeMenu = () => {
            menu.classList.add('opacity-0', 'translate-y-[-10px]', 'pointer-events-none');
            menu.classList.remove('opacity-100', 'translate-y-0', 'pointer-events-auto');
            trigger.classList.remove('ring-2', 'ring-emerald-500/20', 'border-emerald-500');
        };
        
        trigger.addEventListener('click', (e) => {
            e.stopPropagation();
            const isOpen = menu.classList.contains('opacity-100');
            if (isOpen) closeMenu(); else openMenu();
        });
        
        document.addEventListener('click', () => closeMenu());
    });
}
