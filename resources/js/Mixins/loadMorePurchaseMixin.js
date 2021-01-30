export function loadMorePurchaseMixin (record) {
	return {
		props: {
			purchaseDetails: Object,
		},
		data() {
			return {
				localPurchaseDetails: this.purchaseDetails.data,
				purchasePagination: this.purchaseDetails,
			}
		},
		methods: {
			loadMorePurchase() {
				var currentPage = this.purchasePagination.links[0].current_page;
				var id = window.location.href.split('/');

		      // if (this.loadingMore) return;

		      // this.loadingMore = true;

		      axios.get(`/${record}/${id[4]}/edit?page=${currentPage + 1}`)
			      .then(({ data }) => {
		            // Prepending the old messages to the list.
		            this.localPurchaseDetails = [
		            ...this.localPurchaseDetails,
		            ...data.purchaseDetails.data,
		            ];

		            // Update our pagination meta information.
		            this.purchasePagination = data.purchaseDetails;
		         });
		         // .finally(() => this.loadingMore = false);
	      },
	   },
	}
};
