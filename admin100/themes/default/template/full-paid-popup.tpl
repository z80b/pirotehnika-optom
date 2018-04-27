<div class="ps-popup__overlay">
    <div class="ps-popup ps-popup--full-paid js-ps-popup">
        <form class="ps-popup__form ps-form">
            <div class="ps-popup__header">
                <div class="ps-popup__title">Подтверждение оплаты</div>
                <button class="ps-popup__close" type="reset"></button>
            </div>
            <div class="ps-popup__content">
                <div class="ps-form__field">
                    <label class="ps-form__field-title">Дата оплаты</label>
                    <input class="ps-form__input" name="full_paid_date" required/>
                    <span class="ps-form__field-description">Укажите дату оплаты заказа</span>
                </div>
                <div class="ps-form__field">
                    <label class="ps-form__field-title">Сумма оплаты</label>
                    <input class="ps-form__input" name="total_paid_tax_incl" required/>
                    <span class="ps-form__field-description">Укажите сумму оплаты</span>
                </div>
                <div class="ps-form__field">
                    <label class="ps-form__field-title">Причина</label>
                    <textarea class="ps-form__textarea" name="full_paid_reason"></textarea>
                    <span class="ps-form__field-description">Укажите причину установки признака оплаты</span>
                </div>
            </div>
            <div class="ps-popup__footer">
                <button
                    class="ps-popup__submit"
                    name="op"
                    value="update"
                    type="submit">Сохранить</button>
            </div>
        </form>
    </div>
</div>