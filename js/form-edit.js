/**
 * @package Regime
 */
document.regimeFormEdit = {
    form: null,
    emptyModal: null,
    texts: {},
    fieldProps: [
        'placeholder',
        'label',
        'key',
        'value',
        'options',
        'multiple',
        'strict',
        'checked',
        'required',
        'css'
    ],
    fieldsCount: 0,
    fields: {},
    methods: {
        fieldAdd: (type, color, placeholder, bound = false) => {
            let fieldId = '';

            for (let i = 1; i != undefined; i++)
            {
                fieldId = type+'_'+i;

                if (document.regimeFormEdit.fields[fieldId] ==
                    undefined) break;
            }

            document.regimeFormEdit.fields[fieldId] = {};

            const field = document.regimeFormEdit.fields[fieldId];

            field.position = ++document.regimeFormEdit.fieldsCount;
            field.bound = bound;
            field.class = 'table-'+color;
            field.css = '';

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

            document.regimeFormEdit.methods.fieldRowCreate(fieldId);
            document.regimeFormEdit.methods.fieldRowRender(fieldId);
        },
        fieldRowCreate: (fieldId) => {
            const field = document.regimeFormEdit.fields[fieldId];

            const row = document.createElement('tr');

            row.setAttribute('id', fieldId);
            row.setAttribute('class', field.class);
            row.setAttribute('role', 'button');

            document.regimeFormEdit.form.appendChild(row);
        },
        fieldRowRender: (fieldId) => {
            let type = fieldId.split('_');

            type = type[0];

            const row = document.getElementById(fieldId);
            row.innerHTML = '';

            row.appendChild(document.regimeFormEdit.methods.buttonCellRender(
                document.regimeFormEdit.texts.upButton,
                'success',
                'document.regimeFormEdit.methods.fieldMove(\''+fieldId+'\', -1);'
            ));

            row.appendChild(document.regimeFormEdit.methods.buttonCellRender(
                document.regimeFormEdit.texts.downButton,
                'success',
                'document.regimeFormEdit.methods.fieldMove(\''+fieldId+'\');'
            ));

            let prop;

            for (let i = 0; i < document.regimeFormEdit.fieldProps.length; i++)
            {
                prop = document.regimeFormEdit.fieldProps[i];

                if (prop == 'options' ||
                    prop == 'multiple' ||
                    prop == 'strict' ||
                    prop == 'css') continue;

                row.appendChild(
                    document.regimeFormEdit.methods.propCellRender(
                        fieldId,
                        prop
                    )
                );
            }

            row.appendChild(document.regimeFormEdit.methods.buttonCellRender(
                document.regimeFormEdit.texts.deleteButton,
                'danger',
                'document.regimeFormEdit.methods.fieldDelete(\''+fieldId+'\');'
            ));
        },
        buttonCellRender: (text, color, funcname) => {
            const cell = document.createElement('td');

            const button = document.createElement('button');
            button.setAttribute(
                'class',
                'btn btn-sm btn-'+color+' field-func-button'
            );
            button.setAttribute('onclick', funcname);
            button.innerHTML = text;

            cell.appendChild(button);

            return cell;
        },
        propCellRender: (fieldId, prop) => {
            const field = document.regimeFormEdit.fields[fieldId];

            const cell = document.createElement('td');
            cell.setAttribute('id', fieldId+'_'+prop);
            cell.setAttribute(
                'onclick',
                'document.regimeFormEdit.methods.fieldEditingOpen(\''
                    +fieldId+'\');'
            );

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

            methods.modalInputRender(fieldId, 'css');

            if (type != 'checkbox' &&
                type != 'radio') methods
                    .modalInputRender(fieldId, 'placeholder');

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

            if (prop == 'options') value = value.join(';\r');

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
        },
        fieldSave: () => {
            const fieldId = document
                .getElementById('regimeFieldEdit_fieldId')
                .getAttribute('value');
            
            const field = document.regimeFormEdit.fields[fieldId];
            
            const fieldProps = document.regimeFormEdit.fieldProps;

            let propInput;
            let handledList;
            let option;

            for (let i = 0; i < fieldProps.length; i++)
            {
                propInput = document
                    .getElementById('regimeFieldEdit_'+fieldProps[i]);

                if (propInput != undefined)
                {
                    if (propInput.hasAttribute('type'))
                    {
                        if (propInput.getAttribute('type') ==
                            'checkbox') field[fieldProps[i]] = propInput.checked;
                        else field[fieldProps[i]] = propInput.value;
                    }
                    else
                    {
                        field[fieldProps[i]] = propInput.value.split(';');

                        handledList = [];

                        for (let p = 0; p < field[fieldProps[i]].length; p++)
                        {
                            option = field[fieldProps[i]][p].trim();
                            option = option.replace('\n', '');
                            option = option.replace('\r', '');
                            option = option.replace('\t', '');
                            option = option.replace('\0', '');
                            option = option.replace('\v', '');

                            if (option != '') handledList[p] = option;
                        }

                        field[fieldProps[i]] = handledList;
                    }
                }
            }

            document
                .regimeFormEdit.methods.fieldRowRender(fieldId);

            document.regimeFormEdit.methods.toast(
                document.regimeFormEdit.texts.fieldSaveSuccess,
                'success'
            );
        },
        fieldDelete: (fieldId) => {
            if (document
                .regimeFormEdit
                .fields[fieldId].bound) document.regimeFormEdit.methods.toast(
                document.regimeFormEdit.texts.fieldDeleteError,
                'danger'
            );
            else
            {
                const row = document.getElementById(fieldId);

                row.parentNode.removeChild(row);

                delete document.regimeFormEdit.fields[fieldId];

                document.regimeFormEdit.methods.toast(
                    document.regimeFormEdit.texts.fieldDeleteSuccess,
                    'success'
                );
            }
        },
        toast: (text, type) => {
            const toast = document.getElementById('regimeToast');
            const toastText = document.getElementById('regimeToastText');

            toast.setAttribute('class', 'toast hide bg-'+type);

            toastText.innerHTML = text;

            new bootstrap.Toast(toast, []).show();
        },
        formRenderReload: () => {
            const fields = document.regimeFormEdit.fields;

            document.regimeFormEdit.form.innerHTML = '';

            const fieldsEnqueue = {};

            for (fieldId in fields)
            {
                fieldsEnqueue[fields[fieldId].position] = fieldId;
            }

            for (pos in fieldsEnqueue)
            {
                fieldId = fieldsEnqueue[pos];

                document.regimeFormEdit.methods.fieldRowCreate(fieldId);
                document.regimeFormEdit.methods.fieldRowRender(fieldId);
            }
        },
        fieldMove: (fieldId, modus = 1) => {
            modus = modus >= 0 ? 1 : -1;

            const fields = document.regimeFormEdit.fields;

            const fieldsEnqueue = {};

            for (fid in fields)
            {
                fieldsEnqueue[fields[fid].position] = fid;
            }

            const enqueueKeys = Object.keys(fieldsEnqueue);

            const actualPos = fields[fieldId].position * modus;

            let secondFieldId;

            for (let i = actualPos + 1; i != undefined; i++)
            {
                secondFieldId = fieldsEnqueue[i * modus];

                if (secondFieldId != undefined)
                {
                    fields[fieldId].position = fields[secondFieldId].position;
                    fields[secondFieldId].position = actualPos * modus;

                    break;
                }

                if (modus > 0)
                {
                    if (i > enqueueKeys[enqueueKeys.length - 1]) break;
                }
                else if (i == 0) break;
            }

            document.regimeFormEdit.methods.formRenderReload();
        },
        allFormSave: () => {
            const postForm = document.getElementById('regimeFormSave');
            const fields = document.regimeFormEdit.fields;

            const invalidFields = {};

            let type;

            for (fieldId in fields)
            {
                if (fields[fieldId].bound)
                {
                    type = fieldId.split('_');
                    type = type[0];

                    if (type == 'email') fields[fieldId].required = true;
                }

                if (fields[fieldId].key == '')
                {
                    invalidFields[fieldId] = [];

                    invalidFields[fieldId].push('key');
                }
            }

            document.regimeFormEdit.methods.formRenderReload();

            if (JSON.stringify(invalidFields) == '{}')
            {
                const input = document.createElement('input');
                input.setAttribute('type', 'hidden');
                input.setAttribute('name', 'regimeFormFields');
                input.setAttribute(
                    'value',
                    JSON.stringify(fields)
                    );
                
                postForm.appendChild(input);
                
                postForm.submit();
            }
            else
            {
                for (fieldId in invalidFields)
                {
                    for (let i = 0; i < invalidFields[fieldId].length; i++)
                    {
                        document.getElementById(
                            fieldId+'_'+invalidFields[fieldId][i]
                        ).setAttribute('class', 'table-danger');
                    }
                }

                document.regimeFormEdit.methods.toast(
                    document.regimeFormEdit.texts.formSaveError,
                    'danger'
                );
            }
        }
    }
};
