/**
 * @package Regime
 */
document.regimeFormEdit = {
    form: document.getElementById('regimeFormFields'),
    emptyModal: document.getElementById('regimeFieldEditingModal').innerHTML,
    fieldProps: [
        'placeholder',
        'label',
        'key',
        'value',
        'options',
        'multiple',
        'strict',
        'checked',
        'required'
    ],
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

            if (type == 'select' ||
                type == 'datalist') field.options = [];

            if (type == 'select') field.multiple = false;

            if (type == 'datalist') field.strict = false;
            
            field.placeholder = placeholder;

            const row = document.createElement('tr');

            row.setAttribute('id', fieldId);
            row.setAttribute('class', 'table-'+color);
            row.setAttribute('role', 'button');
            row.setAttribute(
                'onclick',
                'document.regimeFormEdit.methods.fieldEditingOpen(\''
                    +fieldId+'\');'
            );

            document.regimeFormEdit.form.appendChild(row);

            document.regimeFormEdit.methods.fieldRowRender(fieldId);
        },
        fieldRowRender: (fieldId) => {
            let type = fieldId.split('_');

            type = type[0];

            const row = document.getElementById(fieldId);
            row.innerHTML = '';

            let cell;
            let prop;

            for (let i = 0; i < document.regimeFormEdit.fieldProps.length; i++)
            {
                prop = document.regimeFormEdit.fieldProps[i];

                if (prop == 'options' ||
                    prop == 'multiple' ||
                    prop == 'strict') continue;

                cell = document
                    .regimeFormEdit.methods.propCellRender(
                        fieldId, prop);

                row.appendChild(cell);
            }
        },
        propCellRender: (fieldId, prop) => {
            const field = document.regimeFormEdit.fields[fieldId];

            const cell = document.createElement('td');
            cell.setAttribute('id', fieldId+'_'+prop);

            if (field[prop] == undefined) cell.innerHTML = '—';
            else
            {
                if (typeof field[prop] ==
                    'string') cell.innerHTML = field[prop];
                else cell.innerHTML = field[prop] == true ? 'Да' : 'Нет';
            }

            return cell;
        },
        fieldEditingOpen: (fieldId) => {
            const methods = document.regimeFormEdit.methods;

            let type = fieldId.split('_');
            type = type[0];

            document.getElementById('regimeFieldEditingModal')
                .innerHTML = document
                .regimeFormEdit.emptyModal;

            let input = document.createElement('input');
            input.setAttribute('type', 'hidden');
            input.setAttribute('id', 'regimeFieldEdit_fieldId');
            input.setAttribute('value', fieldId);

            document.getElementById('regimeFieldEdit_fieldId_block')
                .appendChild(input);

            methods.modalInputRender(fieldId, 'placeholder');

            if (type != 'reset')
            {
                methods.modalInputRender(fieldId, 'label');
                methods.modalInputRender(fieldId, 'key');
                methods.modalInputRender(fieldId, 'required');

                if (type != 'checkbox' &&
                    type != 'radio') methods.modalInputRender(fieldId, 'value');
            }

            if (type == 'select' ||
                type == 'datalist') methods.modalInputRender(fieldId, 'options');

            if (type == 'select') methods.modalInputRender(fieldId, 'multiple');

            if (type == 'datalist') methods.modalInputRender(fieldId, 'strict');

            if (type == 'checkbox' ||
                type == 'radio') methods.modalInputRender(fieldId, 'checked');

            document.getElementById('regimeFieldEditingModalTrigger').click();
        },
        modalInputRender: (fieldId, prop) => {
            const field = document.regimeFormEdit.fields[fieldId];

            const block = document
                .getElementById('regimeFieldEdit_'+prop+'_block');

            if (block.hasAttribute('hidden')) block.removeAttribute('hidden');

            let value = field[prop];

            if (prop == 'options') value = value.join('\n\r');

            const input = document.createElement(
                prop == 'options' ? 'textarea' : 'input'
            );

            input.setAttribute('id', 'regimeFieldEdit_'+prop);

            if (prop != 'options' &&
                prop != 'multiple' &&
                prop != 'strict' &&
                prop != 'checked' &&
                prop != 'required')
            {
                input.setAttribute('type', 'text');
                input.setAttribute('value', value);
            }

            if (prop == 'multiple' ||
                prop == 'strict' ||
                prop == 'checked' ||
                prop == 'required')
            {
                input.setAttribute('type', 'checkbox');

                if (value) input.setAttribute('checked', 'true');

                block.insertBefore(input, block.lastElementChild);
            }
            else
            {
                input.setAttribute('class', 'form-control');

                block.appendChild(input);
            }

            if (prop == 'options') input.innerHTML = value;
        }
    }
};
