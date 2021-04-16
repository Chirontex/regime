/**
 * @package Regime
 */
document.regimeFormEdit = {
    form: document.getElementById('regimeFormFields'),
    fields: {},
    methods: {
        fieldAdd: (type, color, placeholder) => {
            const row = document.createElement('tr');

            let fieldId = '';

            for (let i = 1; i != undefined; i++)
            {
                fieldId = type+'_'+i;

                if (document.regimeFormEdit.fields[fieldId] ==
                    undefined) break;
            }

            row.setAttribute('id', fieldId);
            row.setAttribute('class', 'table-'+color);

            const cell = document.createElement('td');

            cell.innerHTML = placeholder;
            
            if (type != 'reset') cell.innerHTML += ':';

            row.appendChild(cell);

            document.regimeFormEdit.form.appendChild(row);

            document.regimeFormEdit.fields[fieldId] = {};

            const field = document.regimeFormEdit.fields[fieldId];

            if (type != 'reset')
            {
                field.key = '';
                field.label = '';
                field.required = true;
            }

            if (type != 'checkbox' &&
                type != 'radio' &&
                type != 'select') field.placeholder = placeholder;

            if (type != 'checkbox' &&
                type != 'radio' &&
                type != 'reset') field.value = '';

            if (type == 'checkbox' ||
                type == 'radio') field.checked = false;
        }
    }
};
