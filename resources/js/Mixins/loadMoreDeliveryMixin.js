export function loadMoreDeliveryMixin (record) {
	return {
		props: {
			deliveryDetails: Object,
		},
		data() {
			return {
				localDeliveryDetails: this.deliveryDetails.data,
				deliveryPagination: this.deliveryDetails,
			}
		},
		methods: {
	      loadMoreDelivery() {
	       	var currentPage = this.deliveryPagination.links[0].current_page;
	       	var id = window.location.href.split('/');

		      // if (this.loadingMore) return;

		      // this.loadingMore = true;

		      axios.get(`/${record}/${id[4]}/edit?page=${currentPage + 1}`)
			      .then(({ data }) => {
		            // Prepending the old messages to the list.
		            this.localDeliveryDetails = [
		            ...this.localDeliveryDetails,
		            ...data.deliveryDetails.data,
		            ];

		            // Update our pagination meta information.
		            this.deliveryPagination = data.deliveryDetails;
		         });
		          // .finally(() => this.loadingMore = false);
	      },
    	},
 	}
};
