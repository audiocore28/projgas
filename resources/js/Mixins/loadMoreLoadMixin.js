export function loadMoreLoadMixin (record) {
	return {
		props: {
			loadDetails: Object,
		},
		data() {
			return {
				localLoadDetails: this.loadDetails.data,
				loadPagination: this.loadDetails,
			}
		},
		methods: {
			loadMoreLoad() {
				var currentPage = this.loadPagination.links[0].current_page;
				var id = window.location.href.split('/');

		      // if (this.loadingMore) return;

		      // this.loadingMore = true;

		      axios.get(`/${record}/${id[4]}/edit?page=${currentPage + 1}`)
		      	.then(({ data }) => {
		            // Prepending the old messages to the list.
		            this.localLoadDetails = [
		            ...this.localLoadDetails,
		            ...data.loadDetails.data,
		            ];

		            // Update our pagination meta information.
		            this.loadPagination = data.loadDetails;
		         });
		          // .finally(() => this.loadingMore = false);
       	},
    	},
 	}
};
