/**
 * @package Regime
 */
document.regimeFormEdit = {
    form: document.getElementById('regimeFormFields'),
    fields: {},
    methods: {
        fieldAdd: (type, color, placeholder) => {
            let fieldId = '';

            for (let i = 1; i != undefined; i++)
            {
                fieldId = type+'_'+i;

                if (document.regimeFormEdit.fields[fieldId] ==
                    undefined) break;
            }

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
            type != 'reset') field.value = '';
            
            if (type == 'checkbox' ||
            type == 'radio') field.checked = false;
            
            field.placeholder = placeholder;

            document.regimeFormEdit.methods.fieldRowRender(fieldId, color);
        },
        fieldRowRender: (fieldId, color) => {
            let type = fieldId.split('_');

            type = type[0];

            const row = document.createElement('tr');

            row.setAttribute('id', fieldId);
            row.setAttribute('class', 'table-'+color);
            row.setAttribute('role', 'button');

            const cell = document.createElement('td');

            cell.innerHTML = document
                .regimeFormEdit.fields[fieldId].placeholder;
            
            if (type != 'reset') cell.innerHTML += ':';

            row.appendChild(cell);

            document.regimeFormEdit.form.appendChild(row);
        }
    }
};
