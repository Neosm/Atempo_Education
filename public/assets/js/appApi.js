/**
 * @param {HTMLSelectElement} select 
 */
function bindSelect (select) {
    new TomSelect(select, {
        hideSelected: true,
        closeAfterSelect: true,
        maxOptions: 20,
        plugins: {
            remove_button: {
                title: 'Supprimer l\'utilisateur'
            }
        }
    })
}


Array.from(document.querySelectorAll('select[multiple]')).map(bindSelect)