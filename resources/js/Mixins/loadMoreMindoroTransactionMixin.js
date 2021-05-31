export function loadMoreMindoroTransactionMixin (record) {
	return {
		props: {
			mindoroDetails: Object,
		},
		data() {
			return {
				localMindoroDetails: this.mindoroDetails,
				// mindoroPagination: this.mindoroDetails,
			}
		},
		methods: {
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
