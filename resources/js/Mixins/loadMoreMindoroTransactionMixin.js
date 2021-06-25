export function loadMoreMindoroTransactionMixin (record) {
	return {
		props: {
			mindoroDetails: Object,
		},
		data() {
			return {
				localMindoroDetails: this.mindoroDetails,
				// mindoroPagination: this.mindoroDetails,
				form: {
		      	mindoroDetails: this.mindoroDetails,
        			removed_payment_details: [],
		      },
			}
		},
		methods: {
			updateMindoroPayment() {
		      this.$inertia.put(this.route('mindoro-payment-details.update', this.client.id), this.form, {
		        onStart: () => this.sending = true,
		        onFinish: () => this.sending = false,
		      });
		   },

		   addNewPaymentForm(year, month, transactionDetailIndex, mindoroTransactionDetailId) {
		   	this.form.mindoroDetails[year][month][transactionDetailIndex].payments.push({
		        id: null,
		        date: null,
		        mode: null,
		        amount: 0,
		        remarks: null,
		        mindoro_transaction_detail_id: mindoroTransactionDetailId,
		   	});
		   },
		   deletePaymentForm(year, month, transactionDetailIndex, paymentIndex, paymentId) {
		      if (paymentId) {
		        	this.form.removed_payment_details.push(paymentId);
		   		this.form.mindoroDetails[year][month][transactionDetailIndex].payments.splice(paymentIndex, 1);
		      } else {
		   		this.form.mindoroDetails[year][month][transactionDetailIndex].payments.splice(paymentIndex, 1);
		      }
		   },
			loadMoreMindoroTransaction() {
				var currentPage = this.mindoroPagination.links[0].current_page;
				var id = window.location.href.split('/');

		      // if (this.loadingMore) return;

		      // this.loadingMore = true;

		      axios.get(`/${record}/${id[4]}?page=${currentPage + 1}`)
		      	.then(({ data }) => {
		            // Prepending the old messages to the list.
		            this.localMindoroDetails = [
		            ...this.localMindoroDetails,
		            ...data.mindoroDetails.data,
		            ];

		            // Update our pagination meta information.
		            this.mindoroPagination = data.mindoroDetails;
		         });
		          // .finally(() => this.loadingMore = false);
       	},
    	},
 	}
};
