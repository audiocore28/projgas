export function loadMoreHaulMixin (record) {
	return {
		props: {
			haulDetails: Object,
		},
		data() {
			return {
				localHaulDetails: this.haulDetails.data,
				haulPagination: this.haulDetails,
			}
		},
		methods: {
			loadMoreHaul() {
				var currentPage = this.haulPagination.links[0].current_page;
				var id = window.location.href.split('/');

		      // if (this.loadingMore) return;

		      // this.loadingMore = true;

		      axios.get(`/${record}/${id[4]}/edit?page=${currentPage + 1}`)
		      	.then(({ data }) => {
		            // Prepending the old messages to the list.
		            this.localHaulDetails = [
		            ...this.localHaulDetails,
		            ...data.haulDetails.data,
		            ];

		            // Update our pagination meta information.
		            this.haulPagination = data.haulDetails;
		         });
		          // .finally(() => this.loadingMore = false);
       	},
    	},
 	}
};
