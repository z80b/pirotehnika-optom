<div class="ps-popup__overlay">
    <div class="ps-popup ps-popup--full-paid js-ps-popup">
        <form class="ps-popup__form ps-form" method="post">
            <div class="ps-popup__header">
                <div class="ps-popup__title">Подтверждение оплаты</div>
                <button class="ps-popup__close" type="reset"></button>
            </div>
            <div class="ps-popup__content">
                <div class="ps-form__field">
                    <label class="ps-form__field-title">Дата полной оплаты</label>
                    <input class="ps-form__input" type="text" name="full_paid_date" required />
                    <span class="ps-form__field-description">Укажите дату полной оплаты</span>
                </div>
                <div class="ps-form__field">
                    <label class="ps-form__field-title">Полная сумма оплаты</label>
                    <input class="ps-form__input" type="text" name="total_paid_tax_incl" required />
                    <span class="ps-form__field-description">Укажите сумму полной оплаты</span>
                </div>
                <div class="ps-form__field">
                    <label class="ps-form__field-title">Причина</label>
                    <textarea name="full_paid_reason" class="ps-form__textarea" required rows="10"></textarea>
                    <span class="ps-form__field-description">Укажите причину установки признака полной оплаты</span>
                </div>
            </div>
            <div class="ps-popup__footer">
                <button class="ps-popup__submit" type="submit">Сохранить</button>
            </div>
        </form>
    </div>
</div>