/* global Backbone */

(function() {

	OCA.BruteForceSettings = OCA.BruteForceSettings || {};

	OCA.BruteForceSettings.WhitelistCollection = OC.Backbone.Collection.extend({
		model: OCA.BruteForceSettings.WhitelistModel,

		url: OC.generateUrl('/apps/bruteforcesettings/ipwhitelist')
	});
})();
