(function() {
	"use strict";

	/**
	 *  
	 */

	var Event = {
			form: "#eventForm",
			twitter: "#twitterStatuses",
			card: {
				current: "",
				past: "",
				tweet: ""
			},
			params: {
				"event_id": "",
				"event_name": "",
				"event_description": "",
				"event_date": ""
			},
			init: function () {
				this.getCards();
				$(document).ready($.proxy(this.setup, this));
			},
			setup: function (e) {
				Event.getEvents();
				Event.getTwitterUpdates();
				$(document).on("click", "#events [btn=add]", Event.openForm);
				$(document).on("click", "#events [btn=update]", Event.openForm);
				$(document).on("click", "#events [btn=tweet]", Event.tweet);
				$(document).on("click", "#events [btn=delete]", Event.openForm);
				$(document).on("click", Event.form+" [type=submit]", Event.act);
			},
			getCards: function () {
				$.ajax ({
					type: "POST",
					url: "html/event/_currentEvent.html",
					async: false,
					cache: false,
					dataType: "html",
					success: Event.setCurrentCard
				});
				$.ajax ({
					type: "POST",
					url: "html/event/_pastEvent.html",
					async: false,
					cache: false,
					dataType: "html",
					success: Event.setPastCard
				});
				$.ajax ({
					type: "POST",
					url: "html/event/_tweet.html",
					async: false,
					cache: false,
					dataType: "html",
					success: Event.setTweetCard
				});
			},
			setCurrentCard: function (data) {
				Event.card.current = data;
			},
			setPastCard: function (data) {
				Event.card.past = data;
			},
			setTweetCard: function (data) {
				Event.card.tweet = data;
			},
			openForm: function (e) {
				var form = $(Event.form),
				that = $(this),
				card = that.parents(".eventCard"),
				action = that.attr("btn");
				$("[type=submit]", form).val(action);
				$("form", form).get(0).reset();
				try {
					for (var param in Event.params) {
						var value = $("#"+param, card).text();
						$("#"+param, form).val(value.trim());
					}
				} catch (e) {};
				form.modal("show");
			},
			send: function (data, callback) {
				$.ajax ({
					type: "POST",
					url: "server/event/request.php",
					async: true,
					cache: false,
					dataType: "json",
					data: data,
					success: callback
				});
			},
			getEvents: function () {
				Event.send({
					"get_events" : true,
					"get_past_events" : true,
					"statuses_user_timeline" : true
				}, Event.display);
			},
			display: function (data) {
				console.log("Event returned", data);
				var raw_card = Event.card.current,
				current = data["event"]["current"],
				past = data["event"]["past"],
				library_current = $("#eventCards").html(""),
				library_past = $("#pastEvents").html("");
				Event.createCard(Event.card.current, current, library_current);
				Event.createCard(Event.card.past, past, library_past);
			},
			createCard: function (raw_card, events, library) {
				var card = null, 
				event = null;
				if (events)
				for (var i = 0; i < events.length; i++) {
					card = $(raw_card);
					event = events[i];
					event["event_date"] = event["event_date"].replace("00:00:00", "");
					$("#event_formatted_date", card).text(Event.formatDate(event["event_date"]));
					for (var param in Event.params) {
						$("#" + param, card).text(event[param]);
					}
					library.append(card);
				}
			},
			formatDate: function (timestamp) {
				var
				pre_format = timestamp.replace("00:00:00", "").split(/[- :]/),
				post_format = new Date(
						pre_format[0], pre_format[1]-1, pre_format[2]);
				return post_format;
			},
			getData: function (form) {
				var event_data = {};
				for (var param in Event.params) {
					var value = $("#" + param, form).val();
					event_data[param] = value; 
				}
				return event_data;
			},
			act: function (e) {
				e.preventDefault();
				var that = $(this), 
				form = that.parents("form"),
				action = that.val() + "_event",
				event_data = Event.getData(form);
				event_data[action] = true;
				Event.send(event_data, Event.updateEvents);
				Event.tweet(event_data["event_name"] + " - " + event_data["event_description"]);
			},
			updateEvents: function (data) {
				console.log(data);
				$(Event.form).modal('hide');
				Event.getEvents();
				Event.getTwitterUpdates();
			}, 
			twitterRequest: function (data, callback) {
				$.ajax ({
					type: "POST",
					url: "server/twitter/request.php",
					async: true,
					cache: false,
					dataType: "json",
					data: data,
					success: callback
				});
			}, 
			displayTweets: function (data) {
				console.log("Twitter: ", data);
				var raw_card = Event.card.current,
				tweets = data["status"],
				library_tweets = $(Event.twitter).html("");
				Event.createTweet(Event.card.tweet, tweets, library_tweets);
			},
			createTweet: function (raw_card, statuses, library) {
				var card = null,
				status = null;
				if (statuses)
				for (var i = 0; i < statuses.length; i++) {
					card = $(raw_card);
					status = statuses[i];
					$("#status", card).html(status["text"]);
					$("#user", card).html(status["user"]["screen_name"]);
					library.append(card);
				}
			},
			tweet: function (status) {
				console.log("Tweeting", status);
				var data = {
						"statuses_update": true, 
						"status": status
					};
				Event.twitterRequest(data);
			},
			getTwitterUpdates: function () {
				var data = {
						"statuses_user_timeline": true
					};
				Event.twitterRequest(data, Event.displayTweets);
			}
	}
	Event.init();

})();