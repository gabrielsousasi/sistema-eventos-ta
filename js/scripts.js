/* To Atoa - Events – validações e interações (JavaScript puro) */

document.addEventListener('DOMContentLoaded', function () {

    /* ---------- Validação dos formulários [data-validar] ---------- */

    var formatoEmail = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

    function limparErro(campo) {
        var bloco = campo.closest('.campo');
        if (!bloco) { return; }
        bloco.classList.remove('invalido');
        var aviso = bloco.querySelector('.erro-campo');
        if (aviso) { aviso.remove(); }
    }

    function mostrarErro(campo, texto) {
        var bloco = campo.closest('.campo');
        bloco.classList.add('invalido');
        var aviso = document.createElement('small');
        aviso.className = 'erro-campo';
        aviso.textContent = texto;
        bloco.appendChild(aviso);
    }

    function hojeISO() {
        var d = new Date();
        var mes = String(d.getMonth() + 1).padStart(2, '0');
        var dia = String(d.getDate()).padStart(2, '0');
        return d.getFullYear() + '-' + mes + '-' + dia;
    }

    function validarCampo(campo, form) {
        var valor = campo.value.trim();

        if (campo.required && valor === '') {
            return 'Preencha este campo.';
        }
        if (campo.type === 'email' && valor !== '' && !formatoEmail.test(valor)) {
            return 'Informe um e-mail válido, como nome@exemplo.com.';
        }
        if (campo.minLength > 0 && valor !== '' && campo.value.length < campo.minLength) {
            return 'Use pelo menos ' + campo.minLength + ' caracteres.';
        }
        if (campo.dataset.igualA) {
            var outro = form.querySelector('#' + campo.dataset.igualA);
            if (outro && campo.value !== outro.value) {
                return 'As senhas não são iguais.';
            }
        }
        if (campo.hasAttribute('data-futuro') && valor !== '' && valor < hojeISO()) {
            return 'A data não pode estar no passado.';
        }
        return '';
    }

    document.querySelectorAll('form[data-validar]').forEach(function (form) {
        var campos = form.querySelectorAll('input:not([type="hidden"]), textarea');

        campos.forEach(function (campo) {
            campo.addEventListener('input', function () { limparErro(campo); });
        });

        form.addEventListener('submit', function (evento) {
            var primeiroInvalido = null;

            campos.forEach(function (campo) {
                limparErro(campo);
                var erro = validarCampo(campo, form);
                if (erro) {
                    mostrarErro(campo, erro);
                    if (!primeiroInvalido) { primeiroInvalido = campo; }
                }
            });

            if (primeiroInvalido) {
                evento.preventDefault();
                primeiroInvalido.focus();
            }
        });
    });

    /* ---------- Mostrar ou ocultar senha ---------- */

    document.querySelectorAll('.btn-olho').forEach(function (botao) {
        botao.addEventListener('click', function () {
            var campo = document.getElementById(botao.dataset.alvo);
            var oculta = campo.type === 'password';
            campo.type = oculta ? 'text' : 'password';
            botao.textContent = oculta ? 'Ocultar' : 'Mostrar';
            botao.setAttribute('aria-label', (oculta ? 'Ocultar' : 'Mostrar') + ' senha');
        });
    });

    /* ---------- Confirmação antes de cancelar agendamento ---------- */

    document.querySelectorAll('form.form-cancelar').forEach(function (form) {
        form.addEventListener('submit', function (evento) {
            if (!window.confirm('Deseja realmente cancelar este agendamento?')) {
                evento.preventDefault();
            }
        });
    });

    /* ---------- Data mínima no cadastro de evento ---------- */

    document.querySelectorAll('input[data-futuro]').forEach(function (campo) {
        campo.min = hojeISO();
    });

    /* ---------- Menu em telas pequenas ---------- */

    var botaoMenu = document.querySelector('.menu-botao');
    var menu = document.getElementById('menu');
    if (botaoMenu && menu) {
        botaoMenu.addEventListener('click', function () {
            var aberto = menu.classList.toggle('aberto');
            botaoMenu.setAttribute('aria-expanded', aberto ? 'true' : 'false');
        });
    }
});
