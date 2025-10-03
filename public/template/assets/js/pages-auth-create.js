  (function() {
            const passEl = document.getElementById('multiStepsPass');
            const confEl = document.getElementById('multiStepsConfirmPass');
            const passErr = document.getElementById('pass-error');
            const confErr = document.getElementById('confirm-error');
            const submitBtn = document.querySelector('.btn-submit');


            function validatePassword() {
                const v = passEl.value || '';
                const ok = /[A-Z]/.test(v) && /\d/.test(v) && v.length >= 8;
                passErr.classList.toggle('d-none', ok);
                passErr.textContent = ok ? '' : 'รูปแบบรหัสผ่านไม่ถูกต้อง';
                passEl.classList.toggle('is-invalid', !ok);
                passEl.classList.toggle('is-valid', ok);
                return ok;
            }

            function validateConfirm() {
                const ok = (confEl.value || '') === (passEl.value || '');
                confErr.classList.toggle('d-none', ok);
                confErr.textContent = ok ? '' : 'รหัสผ่านและยืนยันรหัสผ่านไม่ตรงกัน';
                confEl.classList.toggle('is-invalid', !ok);
                confEl.classList.toggle('is-valid', ok);
                return ok;
            }

            ['input', 'blur'].forEach(ev => {
                passEl.addEventListener(ev, validatePassword);
                confEl.addEventListener(ev, validateConfirm);
            });

            submitBtn.addEventListener('click', (e) => {
                if (!(validatePassword() && validateConfirm())) {
                    e.preventDefault();
                    e.stopPropagation();
                }
            });
        })();
