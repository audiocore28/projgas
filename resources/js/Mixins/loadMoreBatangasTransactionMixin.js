export function loadMoreBatangasTransactionMixin (record) {
	return {
		props: {
			batangasDetails: Object,
		},
		data() {
			return {
				localBatangasDetails: this.batangasDetails,
				// batangasPagination: this.batangasDetails,
			}
		},
		methods: {
	      loadMoreBatangasTransaction() {
	       	var currentPage = this.batangasPagination.links[0].current_page;
	       	var id = window.location.href.split('/');

		      // if (this.loadingMore) return;

		      // this.loadingMore = true;

		      axios.get(`/${record}/${id[4]}/edit?page=${currentPage + 1}`)
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
