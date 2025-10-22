document.addEventListener('DOMContentLoaded', () => {
    const pass = document.getElementById('reg_pass');
    const btn = document.getElementById('btnEye');
    const icon = document.getElementById('iconEye');

    const eye = '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#444" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1.5 12S5.5 4.5 12 4.5 22.5 12 22.5 12 18.5 19.5 12 19.5 1.5 12 1.5 12Z"/><circle cx="12" cy="12" r="3.5"/></svg>';
    const eyeOff = '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#444" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 3l18 18"/><path d="M1.5 12S5.5 4.5 12 4.5c2.1 0 4.1.5 5.9 1.4M22.5 12S18.5 19.5 12 19.5c-2.1 0-4.1-.5-5.9-1.4"/><circle cx="12" cy="12" r="3.5"/></svg>';

    icon.innerHTML = eye;
    btn.addEventListener('click', () => {
        const t = pass.type === 'password';
        pass.type = t ? 'text' : 'password';
        icon.innerHTML = t ? eyeOff : eye;
    });

    // Filtro ciudades
    const selDepto = document.getElementById('selDepto');
    const selCiudad = document.getElementById('selCiudad');
    const allCities = Array.from(selCiudad.querySelectorAll('option'));

    selDepto.addEventListener('change', () => {
        const dept = selDepto.value;
        selCiudad.disabled = !dept;
        selCiudad.value = '';
        allCities.forEach(o => {
            if (!o.value) return;
            o.hidden = o.getAttribute('data-dept') !== dept;
        });
    });
});