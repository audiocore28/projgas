export const numberFormatsMixin = {
	methods: {
		quantityFormat(value) {
			return Number(value).toLocaleString();
		},
		toPHP(value) {
			return `₱${Number(value).toLocaleString()}`;
		},
		totalCurrency(quantity, unitPrice) {
			return this.toPHP(quantity * unitPrice);
		},
	}
}