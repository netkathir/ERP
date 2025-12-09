<style>
    .toast-backdrop {
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(0, 0, 0, 0.4);
        display: none;
        align-items: center;
        justify-content: center;
        z-index: 10000;
    }
    .toast-backdrop.show {
        display: flex;
    }
    .toast {
        background: #dc3545;
        color: white;
        padding: 18px 24px;
        border-radius: 8px;
        box-shadow: 0 8px 16px rgba(0,0,0,0.25);
        min-width: 320px;
        max-width: 480px;
        text-align: center;
        font-size: 14px;
        font-weight: 500;
    }
</style>
<div id="toast-backdrop" class="toast-backdrop">
    <div id="toast" class="toast"></div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const ratingInputs = document.querySelectorAll('input[name$="_rating"]');
        const totalDisplay = document.getElementById('total_score_display');
        const form = document.querySelector('form');

        // Toast notification function (modal style)
        function showToast(message) {
            const backdrop = document.getElementById('toast-backdrop');
            const toast = document.getElementById('toast');
            if (!toast || !backdrop) return;
            toast.textContent = message;
            backdrop.classList.add('show');
            setTimeout(function() {
                backdrop.classList.remove('show');
            }, 4000);
        }

        // Validate individual rating input
        function validateRating(input) {
            const max = parseInt(input.getAttribute('data-max'), 10);
            const value = parseFloat(input.value);
            const criterion = input.getAttribute('data-criterion') || 'this criterion';
            
            if (isNaN(value) || value < 0) {
                input.value = '';
                return true;
            }
            
            if (value > max) {
                showToast('ActualRated is greater than maxpoint for ' + criterion);
                input.style.borderColor = '#dc3545';
                input.style.borderWidth = '2px';
                input.value = '';
                input.focus();
                return false;
            } else {
                input.style.borderColor = '#ddd';
                input.style.borderWidth = '1px';
                return true;
            }
        }

        // Validate all ratings before form submission
        function validateAllRatings() {
            let isValid = true;
            ratingInputs.forEach(function(input) {
                if (!validateRating(input)) {
                    isValid = false;
                }
            });
            return isValid;
        }

        function recalc() {
            let total = 0;
            ratingInputs.forEach(function (input) {
                const val = parseFloat(input.value || '0');
                if (!isNaN(val) && val >= 0) {
                    const max = parseInt(input.getAttribute('data-max'), 10);
                    if (val <= max) {
                        total += val;
                    }
                }
            });
            if (totalDisplay) {
                totalDisplay.value = total;
            }
        }

        // Add validation on blur and input events
        ratingInputs.forEach(function (input) {
            input.addEventListener('blur', function() {
                validateRating(input);
                recalc();
            });
            input.addEventListener('input', function() {
                recalc();
                const max = parseInt(input.getAttribute('data-max'), 10);
                const value = parseFloat(input.value);
                if (!isNaN(value) && value <= max) {
                    input.style.borderColor = '#ddd';
                    input.style.borderWidth = '1px';
                }
            });
        });

        // Form submission validation
        form.addEventListener('submit', function(e) {
            if (!validateAllRatings()) {
                e.preventDefault();
                showToast('Please correct all rating values before submitting.');
                return false;
            }
        });

        recalc();

        // Subcontractor auto‑populate
        const suppliersData = @json($suppliersData);
        const supplierSelect = document.getElementById('supplier_id');

        function populateSupplierFields(id) {
            const s = suppliersData[id];
            const byId = (x) => document.getElementById(x);
            if (!s) return;
            if (byId('contact_person')) byId('contact_person').value = s.contact_person || '';
            if (byId('address_line_1')) byId('address_line_1').value = s.address_line_1 || '';
            if (byId('address_line_2')) byId('address_line_2').value = s.address_line_2 || '';
            if (byId('city')) byId('city').value = s.city || '';
            if (byId('state')) byId('state').value = s.state || '';
            if (byId('subcontractor_remarks')) byId('subcontractor_remarks').value = s.remarks || '';
        }

        if (supplierSelect) {
            supplierSelect.addEventListener('change', function () {
                if (this.value && suppliersData[this.value]) {
                    populateSupplierFields(this.value);
                }
            });

            if (supplierSelect.value && suppliersData[supplierSelect.value]) {
                populateSupplierFields(supplierSelect.value);
            }
        }
    });
</script>


