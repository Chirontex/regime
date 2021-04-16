/**
 * @package Regime
 */
document.regimeFormEdit = {
    form: document.getElementById('regimeFormFields'),
    fields: {},
    methods: {
        fieldAdd: (type, color, placeholder) => {
            let row = document.createElement('tr');

            row.setAttribute('class', 'table-'+color);

            let cell = document.createElement('td');

            cell.innerHTML = placeholder+':';

            row.appendChild(cell);

            document.regimeFormEdit.form.appendChild(row);
        }
    }
};
