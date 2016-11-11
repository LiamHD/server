(function() {

	OCA.BruteForceSettings = OCA.BruteForceSettings || {};

	OCA.BruteForceSettings.WhiteList = {

		collection: null,
		view: null,

		init: function () {
			this.collection = new OCA.BruteForceSettings.WhitelistCollection();
			this.view = new OCA.BruteForceSettings.WhitelistView({
				collection: this.collection
			});
			this.view.reload();
		}
	};
})();

$(document).ready(function() {
	OCA.BruteForceSettings.WhiteList.init();
});
