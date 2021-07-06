export function loadMoreBatangasTransactionMixin (record) {
	return {
		props: {
			batangasDetails: Object,
		},
		data() {
			return {
				localBatangasDetails: this.batangasDetails,
				// batangasPagination: this.batangasDetails,
		      form: {
		      	batangasDetails: this.batangasDetails,
        			removed_payment_details: [],
		      },
			}
		},
		methods: {
		   updateBatangasPayment() {
		      this.$inertia.put(this.route('batangas-payment-details.update', this.client.id), this.form, {
		        onStart: () => this.sending = true,
		        onFinish: () => this.sending = false,
		      });
		   },

		   addNewPaymentForm(year, month, transactionDetailIndex, batangasTransactionDetailId) {
		   	this.form.batangasDetails[year][month][transactionDetailIndex].payments.push({
		        id: null,
		        date: null,
		        mode: null,
		        amount: 0,
		        remarks: null,
		        is_verified: false,
		        batangas_transaction_detail_id: batangasTransactionDetailId,
		   	});
		   },
		   deletePaymentForm(year, month, transactionDetailIndex, paymentIndex, paymentId) {
		      if (paymentId) {
		        	this.form.removed_payment_details.push(paymentId);
		   		this.form.batangasDetails[year][month][transactionDetailIndex].payments.splice(paymentIndex, 1);
		      } else {
		   		this.form.batangasDetails[year][month][transactionDetailIndex].payments.splice(paymentIndex, 1);
		      }
		   },
	      loadMoreBatangasTransaction() {
	       	var currentPage = this.batangasPagination.links[0].current_page;
	       	var id = window.location.href.split('/');

		      // if (this.loadingMore) return;

		      // this.loadingMore = true;

		      axios.get(`/${record}/${id[4]}?page=${currentPage + 1}`)
			      .then(({ data }) => {
		            // Prepending the old messages to the list.
		            this.localBatangasDetails = [
		            ...this.localBatangasDetails,
		            ...data.batangasDetails.data,
		            ];

		            // Update our pagination meta information.
		            this.batangasPagination = data.batangasDetails;
		         });
		          // .finally(() => this.loadingMore = false);
	      },
    	},
 	}
};
