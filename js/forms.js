/**
 * @package Regime
 */
document.regimeForms = {
    formDelete: null,
    methods: {
        formDelete: (id) => {
            const form = document.regimeForms.formDelete;

            let input = document.createElement('input');
            input.setAttribute('type', 'hidden');
            input.setAttribute('name', 'regimeFormId');
            input.setAttribute('value', id);

            form.appendChild(input);

            form.submit();
        }
    }
};
