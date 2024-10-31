var MyBookingsRESFrontent = {};

var MyBookingsRESFrontent_List_dateRangePickerInstance = null;
var MyBookingsRESFrontent_List_mapData = null;

var MyBookingsRESFrontent_searchParameters = {
    area: "",
    from: "",
    to: "",
    people: "",
    calendarFrom: null,
    calendarTo: null
};

(function($) {

    // Parameters must be strings
	// Order of must be either 'H' (Highest) or 'L' (Lowest)
	MyBookingsRESFrontent.DOYPSort = function(wrapper, elementtosort, AttrToSort, orderof) {
		$(wrapper).find(elementtosort).sort(function (a, b) {
			if (orderof === 'H') {
				return +b.getAttribute(AttrToSort) - +a.getAttribute(AttrToSort);
			} 
			if (orderof === 'L') {
				return +a.getAttribute(AttrToSort) - +b.getAttribute(AttrToSort);
			}
		}).appendTo(wrapper);
    };
    
    MyBookingsRESFrontent.initSearchCalendar = function(actionAfterSelect) {

        $("#mybookingsres-list-searchbox-date").caleran({
            showFooter: false,
            format: "DD.MM.YYYY",
            autoCloseOnSelect: true,
            startEmpty: true,
            onafterselect: function(caleran, startDate, endDate){
                // caleran: caleran object instance
                // startDate: moment.js instance
                // endDate: moment.js instance
                console.log(startDate.format("DD.MM.YYYY"));
                console.log(endDate.format("DD.MM.YYYY"));
                MyBookingsRESFrontent_searchParameters.calendarFrom = startDate.format("DD.MM.YYYY");
                MyBookingsRESFrontent_searchParameters.calendarTo= endDate.format("DD.MM.YYYY");
                
                if(actionAfterSelect != null) {
                    actionAfterSelect();
                }
            },
            oninit: function (instance) {
                MyBookingsRESFrontent_List_dateRangePickerInstance = instance;
                MyBookingsRESFrontent.List.setSearchFields();
            },
            nextMonthIcon: "<div style=\'width:20px\'><svg aria-hidden=\'true\' focusable=\'false\' data-prefix=\'fas\' data-icon=\'arrow-right\' role=\'img\' xmlns=\'http://www.w3.org/2000/svg\' viewBox=\'0 0 448 512\' class=\'svg-inline--fa fa-arrow-right fa-w-14\'><path fill=\'currentColor\' d=\'M190.5 66.9l22.2-22.2c9.4-9.4 24.6-9.4 33.9 0L441 239c9.4 9.4 9.4 24.6 0 33.9L246.6 467.3c-9.4 9.4-24.6 9.4-33.9 0l-22.2-22.2c-9.5-9.5-9.3-25 .4-34.3L311.4 296H24c-13.3 0-24-10.7-24-24v-32c0-13.3 10.7-24 24-24h287.4L190.9 101.2c-9.8-9.3-10-24.8-.4-34.3z\' class=\'\'></path></svg></div>",
            prevMonthIcon: "<div style=\'width:20px\'><svg aria-hidden=\'true\' focusable=\'false\' data-prefix=\'fas\' data-icon=\'arrow-left\' role=\'img\' xmlns=\'http://www.w3.org/2000/svg\' viewBox=\'0 0 448 512\' class=\'svg-inline--fa fa-arrow-left fa-w-14\'><path fill=\'currentColor\' d=\'M257.5 445.1l-22.2 22.2c-9.4 9.4-24.6 9.4-33.9 0L7 273c-9.4-9.4-9.4-24.6 0-33.9L201.4 44.7c9.4-9.4 24.6-9.4 33.9 0l22.2 22.2c9.5 9.5 9.3 25-.4 34.3L136.6 216H424c13.3 0 24 10.7 24 24v32c0 13.3-10.7 24-24 24H136.6l120.5 114.8c9.8 9.3 10 24.8.4 34.3z\' class=\'\'></path></svg></div>",
            rangeIcon: "<div style=\'width:20px\'><svg aria-hidden=\'true\' focusable=\'false\' data-prefix=\'fas\' data-icon=\'retweet\' role=\'img\' xmlns=\'http://www.w3.org/2000/svg\' viewBox=\'0 0 640 512\' class=\'svg-inline--fa fa-retweet fa-w-20\'><path fill=\'currentColor\' d=\'M629.657 343.598L528.971 444.284c-9.373 9.372-24.568 9.372-33.941 0L394.343 343.598c-9.373-9.373-9.373-24.569 0-33.941l10.823-10.823c9.562-9.562 25.133-9.34 34.419.492L480 342.118V160H292.451a24.005 24.005 0 0 1-16.971-7.029l-16-16C244.361 121.851 255.069 96 276.451 96H520c13.255 0 24 10.745 24 24v222.118l40.416-42.792c9.285-9.831 24.856-10.054 34.419-.492l10.823 10.823c9.372 9.372 9.372 24.569-.001 33.941zm-265.138 15.431A23.999 23.999 0 0 0 347.548 352H160V169.881l40.416 42.792c9.286 9.831 24.856 10.054 34.419.491l10.822-10.822c9.373-9.373 9.373-24.569 0-33.941L144.971 67.716c-9.373-9.373-24.569-9.373-33.941 0L10.343 168.402c-9.373 9.373-9.373 24.569 0 33.941l10.822 10.822c9.562 9.562 25.133 9.34 34.419-.491L96 169.881V392c0 13.255 10.745 24 24 24h243.549c21.382 0 32.09-25.851 16.971-40.971l-16.001-16z\' class=\'\'></path></svg></div>",
            headerSeparator: "<div style=\'width:20px\'><svg aria-hidden=\'true\' focusable=\'false\' data-prefix=\'fas\' data-icon=\'chevron-right\' role=\'img\' xmlns=\'http://www.w3.org/2000/svg\' viewBox=\'0 0 320 512\' class=\'svg-inline--fa fa-chevron-right fa-w-10\'><path fill=\'currentColor\' d=\'M285.476 272.971L91.132 467.314c-9.373 9.373-24.569 9.373-33.941 0l-22.667-22.667c-9.357-9.357-9.375-24.522-.04-33.901L188.505 256 34.484 101.255c-9.335-9.379-9.317-24.544.04-33.901l22.667-22.667c9.373-9.373 24.569-9.373 33.941 0L285.475 239.03c9.373 9.372 9.373 24.568.001 33.941z\' class=\'\'></path></svg></div>"
        });

    },

    MyBookingsRESFrontent.webRequest = function(requestData, successCallback, errorCallback) {
        return $.ajax ({
            method: 'post',
            url: ajax.url,
            dataType: "json",
            data: requestData,
            success: function (d) {
                
                if (successCallback != null) {
                    successCallback(d);
                }
            },
            error: function() {
                if (errorCallback != null) {
                    errorCallback();
                }
            }
        });
    };


    MyBookingsRESFrontent.Availability = {
        check: function(searchParameter, categories, successCallback, errorCallback) {
            console.log(searchParameter);
            return $.ajax ({
                method: 'post',
                url: ajax.url,
                dataType: "json",
                data: {
                    action: 'MyBookingsRES_checkAvailability',
                    from: searchParameter.from, // '03.12.2020',
                    to: searchParameter.to, // '04.12.2020',
                    people: searchParameter.people,
                    categories: categories
                },
                success: function (d) {
                    if(successCallback != null) {
                       successCallback(d);
                   }
                },
                error: function() {
                    //any error to be handled
                    if(errorCallback != null) {
                        errorCallback(d);
                   }
                }
            });
        }
    };
    
    MyBookingsRESFrontent.DetailPage = {

        availabilityCheckRequest: null, 
        currentCategory: null,
        currentServerResult: null,

        init: function() {
            if (MyBookingsRESFrontent_searchParameters.from.length > 0 &&
                MyBookingsRESFrontent_searchParameters.to.length > 0 &&
                    MyBookingsRESFrontent_searchParameters.people.length > 0) {

                        MyBookingsRESFrontent.DetailPage.checkAvailability();
            }

            $(".MyBookingsRESFrontent_categoryPage_bookNowButton").click(function(e) {
                e.preventDefault();
                if($(this).data("available") == "1" && MyBookingsRESFrontent.DetailPage.currentServerResult != null) {
                    location.href = "https://www.my-bookings.cc/booking/custom/mb/site.php?tid=" + MyBookingsRESFrontent.DetailPage.currentServerResult.transid + "&lang=" + MyBookingsRESFrontent_lang  + "&category=" + MyBookingsRESFrontent.DetailPage.currentCategory;
                }
            });
        },

        checkAvailability: function() {
           
            if (MyBookingsRESFrontent_searchParameters.calendarFrom != null &&
                MyBookingsRESFrontent_searchParameters.calendarTo != null) {
                        
                MyBookingsRESFrontent_searchParameters.from = MyBookingsRESFrontent_searchParameters.calendarFrom;
                MyBookingsRESFrontent_searchParameters.to = MyBookingsRESFrontent_searchParameters.calendarTo;
            }

            MyBookingsRESFrontent.DetailPage.currentServerResult = null;

            MyBookingsRESFrontent_searchParameters.people = $("#mybookingsres-list-searchbox-people").val();

            if (MyBookingsRESFrontent.DetailPage.availabilityCheckRequest != null) {
                MyBookingsRESFrontent.DetailPage.availabilityCheckRequest.abort();
                MyBookingsRESFrontent.DetailPage.availabilityCheckRequest = null;
            }
            
            $(".mybookingsres-category-pricedetails:not([class~='mybookingsres-is-loading']").addClass("mybookingsres-is-loading");
            $(".mybookingsres-category-pricedetails").removeClass("mybookingsres-notavailable").removeClass("mybookingsres-isavailable");
            $(".mybookingsres-category-pricedetails-list").html("");

            /** check price and availability */
            MyBookingsRESFrontent.List.availabilityCheckRequest = MyBookingsRESFrontent.Availability.check(MyBookingsRESFrontent_searchParameters, [ MyBookingsRESFrontent.DetailPage.currentCategory ], MyBookingsRESFrontent.DetailPage.checkSearchResult);

        },

        getPricedetailsItem: function(col1, col2, className) {
            var addClass = "";
            if(typeof(className) != "undefined" && className.length > 0) {
                addClass = className;
            }
            return '<div class="mybookingsres-category-pricedetails-list-item ' + addClass + '"><div class="mybookingsres-category-pricedetails-list-item-col1">' + col1 + '</div><div class="mybookingsres-category-pricedetails-list-item-col2">' + col2 + ' € </div></div>';
        },

        checkSearchResult: function(d) {
            if (d.error == 0 && d.data.data.error.length === 0) {
                var category, curr, currSearchdetails, isNotAvailable, priceDetails, extrarates;

                category = d.info[0];

                $(".mybookingsres-category-pricedetails").removeClass("mybookingsres-is-loading");

                isNotAvailable = true;

                MyBookingsRESFrontent.DetailPage.currentServerResult = d.data.data;

                if (typeof(d.data.data.rooms_details[category]) !== 'undefined') {
                    curr = d.data.data.rooms_details[category];
                    currSearchdetails = d.data.data.searchdetails;
                    priceDetails = curr.price_details;
    
                    if(curr.rooms_available >= 1) {
                        isNotAvailable = false;

                        $(".mybookingsres-category-pricedetails:not([class~='mybookingsres-isavailable']").addClass("mybookingsres-isavailable");
 
                        $(".mybookingsres-category-pricedetails-list").append(MyBookingsRESFrontent.DetailPage.getPricedetailsItem(currSearchdetails.nights + " Nächte", priceDetails.detail_info.roomcost_f));
                        
                        if (priceDetails.detail_info.extra_charges_for_additional_persons > 0) {
                            $(".mybookingsres-category-pricedetails-list").append(MyBookingsRESFrontent.DetailPage.getPricedetailsItem("Mehrpersonenzuschlag", priceDetails.detail_info.extra_charges_for_additional_persons_f));
                        }

                        if (priceDetails.detail_info.local_tax_as_extra && priceDetails.detail_info.local_tax > 0) {
                            $(".mybookingsres-category-pricedetails-list").append(MyBookingsRESFrontent.DetailPage.getPricedetailsItem("Ortstaxe", priceDetails.detail_info.local_tax_f));
                        }

                        extrarates = priceDetails.detail_info.extrarates;
                        for(var e = 0; e < extrarates.length; e++) {
                            $(".mybookingsres-category-pricedetails-list").append(MyBookingsRESFrontent.DetailPage.getPricedetailsItem(extrarates[e].txt["de"].rate_txt, extrarates[e].price_f));
                        }

                        if (priceDetails.detail_info.discount > 0) {
                            $(".mybookingsres-category-pricedetails-list").append(MyBookingsRESFrontent.DetailPage.getPricedetailsItem("Rabatt", "-" + priceDetails.detail_info.discount_f));
                        }

                        if (curr.non_ref_active == 1) {
                            $(".mybookingsres-category-pricedetails-list").append(MyBookingsRESFrontent.DetailPage.getPricedetailsItem("Nicht erstattbar", "-" + priceDetails.detail_info.non_ref_diff_f));
                        }
                        
                        $(".mybookingsres-category-pricedetails-list").append(MyBookingsRESFrontent.DetailPage.getPricedetailsItem("Gesamtbetrag", priceDetails.totalcost_f, "mybookingsres-category-pricedetails-list-totalcost"));

                    } 

                }

                if (isNotAvailable) {
                    $(".mybookingsres-category-pricedetails:not([class~='mybookingsres-notavailable']").addClass("mybookingsres-notavailable");
                }

            } else {
                console.log("FEHLER: " + d.data.data.error);
                alert("Some error occurred");
            }
        }

    };

    MyBookingsRESFrontent.List = {
    
        categories: [],
        availabilityCheckPackages: [], 
        availabilityCheckPackagesIndex: -1, 
        availabilityCheckPackagesCount: 0, 
        availabilityCheckRequest: null, 
        listContainer: null,

        init: function() {
            MyBookingsRESFrontent.List.listContainer = $("#mybookingsres-list-container-items");
            MyBookingsRESFrontent.List.load();
            MyBookingsRESFrontent.List.AttributeFilter.init();
        },

        load: function() {

            if (MyBookingsRESFrontent.List.availabilityCheckRequest != null) {
                MyBookingsRESFrontent.List.availabilityCheckRequest.abort();
                MyBookingsRESFrontent.List.availabilityCheckRequest = null;
            }

            MyBookingsRESFrontent.List.availabilityCheckPackages = MyBookingsRESFrontent.List.categories.MyBookingsRESFrontent_chunk(5);
            MyBookingsRESFrontent.List.availabilityCheckPackagesCount = MyBookingsRESFrontent.List.availabilityCheckPackages.length;
            MyBookingsRESFrontent.List.availabilityCheckPackagesIndex = -1;

            if (MyBookingsRESFrontent_searchParameters.from.length > 0 &&
                MyBookingsRESFrontent_searchParameters.to.length > 0 &&
                    MyBookingsRESFrontent_searchParameters.people.length > 0) {

                MyBookingsRESFrontent.List.listContainer.removeClass("mybookingsres-dontshow");
                MyBookingsRESFrontent.List.startAvailabilityCheck();
            }
        },

        reload: function(isSearch) {
            
            if (MyBookingsRESFrontent.List.availabilityCheckRequest != null) {
                MyBookingsRESFrontent.List.availabilityCheckRequest.abort();
                MyBookingsRESFrontent.List.availabilityCheckRequest = null;
            }

            MyBookingsRESFrontent.List.availabilityCheckPackagesIndex = -1;

            MyBookingsRESFrontent.DOYPSort("#mybookingsres-list-container-items", ".mybookingsres-list-item", "data-sort", "L");
            $(".mybookingsres-list-item").removeClass("mb-not-available").removeClass("mybookingsres-is-search").removeClass("mybookingsres-is-nosearch");

            if (isSearch) {
                $(".mybookingsres-list-item:not([class~='mybookingsres-is-loading']").addClass("mybookingsres-is-loading");
            } else {
                $(".mybookingsres-list-item:not([class~='mybookingsres-is-nosearch']").addClass("mybookingsres-is-nosearch");
                MyBookingsRESFrontent_List_dateRangePickerInstance.globals.firstValueSelected = true;
                MyBookingsRESFrontent_List_dateRangePickerInstance.config.startDate = null;
                MyBookingsRESFrontent_List_dateRangePickerInstance.config.endDate = null;
                MyBookingsRESFrontent_List_dateRangePickerInstance.reDrawCalendars();

                MyBookingsRESFrontent.List.AttributeFilter.reset();
            }
        },

        setSearchFields() {

            if (MyBookingsRESFrontent_searchParameters.from.length > 0 &&
                MyBookingsRESFrontent_searchParameters.to.length > 0 &&
                    MyBookingsRESFrontent_searchParameters.people.length > 0) {

                    MyBookingsRESFrontent_List_dateRangePickerInstance.globals.firstValueSelected = true;
                    MyBookingsRESFrontent_List_dateRangePickerInstance.config.startDate = moment(MyBookingsRESFrontent_searchParameters.from, "DD.MM.YYYY");
                    MyBookingsRESFrontent_List_dateRangePickerInstance.config.endDate = moment(MyBookingsRESFrontent_searchParameters.to, "DD.MM.YYYY");
                    MyBookingsRESFrontent_List_dateRangePickerInstance.reDrawCalendars();

                    $("#mybookingsres-list-searchbox-people").val(MyBookingsRESFrontent_searchParameters.people);
            }
        },

        startAvailabilityCheck: function() {
            
            MyBookingsRESFrontent.List.availabilityCheckPackagesIndex++;

            console.log(MyBookingsRESFrontent.List.availabilityCheckPackages[MyBookingsRESFrontent.List.availabilityCheckPackagesIndex]);
            let currentPackage = JSON.parse(JSON.stringify(MyBookingsRESFrontent.List.availabilityCheckPackages[MyBookingsRESFrontent.List.availabilityCheckPackagesIndex])).map(function(element) { 
                return element.category 
            });

            $(".mybookingsres-list-item").each(function(e) {
                $(this).find(".mybookingsres-titlelink").attr("href", $(this).data("permalink") + "?a=" + MyBookingsRESFrontent_searchParameters.area + "&f=" + MyBookingsRESFrontent_searchParameters.from + "&t=" + MyBookingsRESFrontent_searchParameters.to + "&people=" + MyBookingsRESFrontent_searchParameters.people + "");
                console.log();
            });

            /** check price and availability */
            MyBookingsRESFrontent.List.availabilityCheckRequest = MyBookingsRESFrontent.Availability.check(MyBookingsRESFrontent_searchParameters, currentPackage, MyBookingsRESFrontent.List.updateAvailabilityInfo);
        },

        updateAvailabilityInfo: function(d) {
            console.log('updateAvailabilityInfo', d);
            if (d.error == 0 && d.data.data.error.length === 0) {
                var category, label, curr, currSearchdetails, isNotAvailable;

                for (var x in d.info) {
                    category = d.info[x];

                    $(".mybookingsres-list-item.entry-" + category).removeClass("mybookingsres-is-loading");

                    //console.log($(".mybookingsres-list-item.entry-" + category).data("permalink"));
                    isNotAvailable = true;

                    if (typeof(d.data.data.rooms_details[category]) !== 'undefined') {
                        curr = d.data.data.rooms_details[category];
                        currSearchdetails = d.data.data.searchdetails;
        
                        $(".mybookingsres-list-item.entry-" + category  + " .mybookingsres-available-rooms .count").html(curr.rooms_available);
                        $(".mybookingsres-list-item.entry-" + category + ":not([class~='mybookingsres-is-search'])").addClass("mybookingsres-is-search");
                        
                        if(curr.rooms_available >= 1) {
                            isNotAvailable = false;
                            $(".mybookingsres-list-item.entry-" + category  + " .mybookingsres-priceinfo-search .price").html(curr.price_details.totalcost_f);
                            $(".mybookingsres-list-item.entry-" + category  + " .mybookingsres-priceinfo-search .nights").html(currSearchdetails.nights);
                            
                        } 

                    }

                    if (isNotAvailable) {
                        if (d.data.hideUnavailableCategories === '1') {
                            $(".mybookingsres-list-item.entry-" + category + ":not([class~='mb-not-available-hidden'])").addClass("mb-not-available-hidden");
                        } else {
                            $(".mybookingsres-list-item.entry-" + category + ":not([class~='mb-not-available'])").addClass("mb-not-available");
                        }
                        
                        $($(".mybookingsres-list-item.entry-" + category).detach()).appendTo(MyBookingsRESFrontent.List.listContainer);
                    }
                }

                if (MyBookingsRESFrontent.List.availabilityCheckPackagesIndex < MyBookingsRESFrontent.List.availabilityCheckPackagesCount-1) {
                    MyBookingsRESFrontent.List.startAvailabilityCheck();
                } else {
                }
            } else {
                console.log("FEHLER: " + d.data.data.error);
                alert("Some error occurred");
            }
        },

        updateGUI: function(category) {
            let item = $(".mybookingsres-list-item.entry-" + category + " .mybookingsres-description");

            let imageHeight = item.find(".mybookingsres-list-item-content img").height();
            let titleHeight = item.find(".mybookingsres-title").outerHeight(true);
            let showMoreHeight = item.find(".mybookingsres-show-more").outerHeight(true);

            let customHeight = (imageHeight - (titleHeight + showMoreHeight)) - 1;

            item.height(customHeight);
        },

        bookNowButton_click(category, isSearch) {
            let url = $(".mybookingsres-list-item.entry-" + category).data("permalink");

            if (isSearch) {
                url += "?a=" + MyBookingsRESFrontent_searchParameters.area + "&f=" + MyBookingsRESFrontent_searchParameters.from + "&t=" + MyBookingsRESFrontent_searchParameters.to + "&people=" + MyBookingsRESFrontent_searchParameters.people;
            }
            location.href = url;
        },

        showEntriesWithCategory: function(categories) {
            $(".mybookingsres-list-item").hide();

            for(var i = 0; i < categories.length; i++) {
                $('.mybookingsres-list-item[data-category="' + categories[i] + '"]').show();
            }

        },

        GMap: {
            
            map: null,
            bounds: null,

            load: function() {
                MyBookingsRESFrontent.List.GMap.map = new google.maps.Map(document.getElementById("mybookingsres-list-map"), {
                    center: { lat: -34.397, lng: 150.644 },
                    zoom: 8,
                });

                var marker = null, 
                    curr,
                    latLon = null;

                MyBookingsRESFrontent.List.GMap.bounds = new google.maps.LatLngBounds();

                for(var i = 0; i < MyBookingsRESFrontent_List_mapData.length; i++) {
                    
                    curr = MyBookingsRESFrontent_List_mapData[i];
                    latLon = new google.maps.LatLng(curr[0], curr[1]);

                    marker = new google.maps.Marker({
                        position: latLon,
                        map: MyBookingsRESFrontent.List.GMap.map,
                        title: curr[2],
                    });

                    MyBookingsRESFrontent.List.GMap.bounds.extend(latLon);
                }

                MyBookingsRESFrontent.List.GMap.updateViewport();

            },
            
            updateViewport: function() {

                console.log("L", MyBookingsRESFrontent.List.GMap.bounds);
                
                MyBookingsRESFrontent.List.GMap.map.fitBounds(MyBookingsRESFrontent.List.GMap.bounds);
                MyBookingsRESFrontent.List.GMap.map.panToBounds(MyBookingsRESFrontent.List.GMap.bounds);
                
                var zoom = MyBookingsRESFrontent.List.GMap.map.getZoom();

                if(zoom > 18) {
                    MyBookingsRESFrontent.List.GMap.map.setZoom(18);
                }
                console.log(zoom);

            }
        },

        AttributeFilter: {

            req: null,

            init: function() {
                var data = {
                    action: "MyBookingsRES_getAttributesForSearch"
                };

                MyBookingsRESFrontent.webRequest(data, function(d) {
                    if(d.error === 0) {
                        var attributes = d.data, attrText = "";

                        for(var a = 0; a < attributes.length; a++) {
                            attrText = attributes[a].desc_en;
                            if(MyBookingsRESFrontent_lang == "de") {
                                attrText = attributes[a].desc_de;
                            }
                            
                            $(".mybookingsres-list-attributes-container-items").append('<div class="mybookingsres-list-attributes-container-item"><input type="checkbox" class="mybookingsres-list-attributes-container-item-chk" id="mybookingsres-list-attributes-container-item-chk-' + attributes[a].attribut_id + '" value="' + attributes[a].attribut_id + '" /> <label for="mybookingsres-list-attributes-container-item-chk-' + attributes[a].attribut_id + '">' + attrText + '</label></div>');  
                        }

                        $(".mybookingsres-list-attributes-container-item-chk").change(function (e) {
                            MyBookingsRESFrontent.List.AttributeFilter.run();
                        });
                    }
                });
            },

            reset: function() {
                $(".mybookingsres-list-attributes-container-item-chk").prop("checked", false);
                MyBookingsRESFrontent.List.AttributeFilter.run();
            },

            run: function() {

                var attributesForFilter = $('.mybookingsres-list-attributes-container-item-chk:checkbox:checked').map(function() {
                    return this.value;
                }).get();

                var data = {
                    action: "MyBookingsRES_getCategoriesWithAttributes",
                    attributesForFilter: attributesForFilter
                };

                if (MyBookingsRESFrontent.List.AttributeFilter.req != null) {
                    MyBookingsRESFrontent.List.AttributeFilter.req.abort();
                    MyBookingsRESFrontent.List.AttributeFilter.req = null;
                }

                MyBookingsRESFrontent.List.AttributeFilter.req = MyBookingsRESFrontent.webRequest(data, function(d) {
                    if(d.error === 0) {
                        MyBookingsRESFrontent.List.showEntriesWithCategory(d.data);
                    }
                });
            }

        }
    };

    MyBookingsRESFrontent.initSearchShortCode = function() {

        let lang = document.getElementsByTagName('html')[0].getAttribute('lang').substr(0, 2);

        let yearOldLabel = lang === 'de' ? 'jahr alt' : 'year old';
        let yearsOldLabel = lang === 'de' ? 'jahre alt' : 'years old';

        const getIconUrl = function(name) {
            let fullUrl = window.location;
            return fullUrl.protocol + "//" + fullUrl.host + "/wp-content/plugins/my-bookings-res/includes/media/icons/" + name + ".svg";
        };

        const findGetParameter = function(parameterName) {
            let result = null,
                tmp = [];
            let items = location.search.substr(1).split("&");
            for (let index = 0; index < items.length; index++) {
                tmp = items[index].split("=");
                if (tmp[0] === parameterName) result = decodeURIComponent(tmp[1]);
            }
            return result;
        };

        const defaultDatePickerContent =
            '<img src="'+ getIconUrl("calendar") +'" alt="calendar">' +
            '<div id="MyBookingsRES-search-box-from">' + (lang === "de" ? "ANREISE" : "ARRIVAL") + '</div>' +
            '<div style="margin-bottom: 3px;">&rarr;</div>' +
            '<div id="MyBookingsRES-search-box-to">' + (lang === "de" ? "ABREISE" : "DEPARTURE") + '</div>';

        let caleranStartDate = '';
        let caleranEndDate = '';

        let initialFrom = $('#MyBookingsRES-search-box-from').html();
        let initialTo = $('#MyBookingsRES-search-box-to').html();

        if (initialFrom && initialTo) {
            caleranStartDate = initialFrom;
            caleranEndDate = initialTo;
        }

        let getParameterPersonCount = findGetParameter('mbp');
        let getParameterChildrenCount = findGetParameter('mbc');

        if (!getParameterPersonCount) {
            getParameterPersonCount = 2;
        }

        if (!getParameterChildrenCount) {
            getParameterChildrenCount = 0;
        }

        let getParameterAdultCount = getParameterPersonCount - getParameterChildrenCount;
        let getParameterChildrenAges = findGetParameter('mbca');

        if (getParameterChildrenAges) {
            getParameterChildrenAges = getParameterChildrenAges.split(',');
        }

        $("#MyBookingsRES-search-box-guests-result").html(getParameterAdultCount);

        $("#MyBookingsRES-search-box-adults-result").html(getParameterAdultCount);
        $("#MyBookingsRES-search-box-adults").html(getParameterAdultCount);

        $("#MyBookingsRES-search-box-children-result").html(getParameterChildrenCount);
        $("#MyBookingsRES-search-box-children").html(getParameterChildrenCount);

        for (let i = 0; i < getParameterChildrenCount; i++) {
            $(".MyBookingsRES-search-box-children-ages").append(
                '<div>' +
                    '<label for="MyBookingsRES-child-'+ i +'">'
                    +   (lang === 'de' ? ('Kind ' + i + ' alter') : ('Child ' + i + ' age')) +
                    '</label>' +
                    '<select id="MyBookingsRES-child-'+ i +'">' +
                        '<option value="1">1 '+ yearOldLabel +'</option>' +
                        '<option value="2">2 '+ yearsOldLabel +'</option>' +
                        '<option value="3">3 '+ yearsOldLabel +'</option>' +
                        '<option value="4">4 '+ yearsOldLabel +'</option>' +
                        '<option value="5">5 '+ yearsOldLabel +'</option>' +
                        '<option value="6">6 '+ yearsOldLabel +'</option>' +
                        '<option value="7">7 '+ yearsOldLabel +'</option>' +
                        '<option value="8">8 '+ yearsOldLabel +'</option>' +
                        '<option value="9">9 '+ yearsOldLabel +'</option>' +
                        '<option value="10">10 '+ yearsOldLabel +'</option>' +
                        '<option value="11">11 '+ yearsOldLabel +'</option>' +
                        '<option value="12">12 '+ yearsOldLabel +'</option>' +
                        '<option value="13">13 '+ yearsOldLabel +'</option>' +
                        '<option value="14">14 '+ yearsOldLabel +'</option>' +
                        '<option value="15">15 '+ yearsOldLabel +'</option>' +
                        '<option value="16">16 '+ yearsOldLabel +'</option>' +
                        '<option value="17">17 '+ yearsOldLabel +'</option>' +
                    '</select>' +
                '</div>'
            );

            $("#MyBookingsRES-child-" + i).val(getParameterChildrenAges[i]);
        }

        let caleranInstance = $("#MyBookingsRES-search-box-dates").caleran({
            showFooter: false,
            format: "DD.MM.YYYY",
            autoCloseOnSelect: true,
            startEmpty: true,
            nextMonthIcon: "<div style=\'width:20px\'><svg aria-hidden=\'true\' focusable=\'false\' data-prefix=\'fas\' data-icon=\'arrow-right\' role=\'img\' xmlns=\'http://www.w3.org/2000/svg\' viewBox=\'0 0 448 512\' class=\'svg-inline--fa fa-arrow-right fa-w-14\'><path fill=\'currentColor\' d=\'M190.5 66.9l22.2-22.2c9.4-9.4 24.6-9.4 33.9 0L441 239c9.4 9.4 9.4 24.6 0 33.9L246.6 467.3c-9.4 9.4-24.6 9.4-33.9 0l-22.2-22.2c-9.5-9.5-9.3-25 .4-34.3L311.4 296H24c-13.3 0-24-10.7-24-24v-32c0-13.3 10.7-24 24-24h287.4L190.9 101.2c-9.8-9.3-10-24.8-.4-34.3z\' class=\'\'></path></svg></div>",
            prevMonthIcon: "<div style=\'width:20px\'><svg aria-hidden=\'true\' focusable=\'false\' data-prefix=\'fas\' data-icon=\'arrow-left\' role=\'img\' xmlns=\'http://www.w3.org/2000/svg\' viewBox=\'0 0 448 512\' class=\'svg-inline--fa fa-arrow-left fa-w-14\'><path fill=\'currentColor\' d=\'M257.5 445.1l-22.2 22.2c-9.4 9.4-24.6 9.4-33.9 0L7 273c-9.4-9.4-9.4-24.6 0-33.9L201.4 44.7c9.4-9.4 24.6-9.4 33.9 0l22.2 22.2c9.5 9.5 9.3 25-.4 34.3L136.6 216H424c13.3 0 24 10.7 24 24v32c0 13.3-10.7 24-24 24H136.6l120.5 114.8c9.8 9.3 10 24.8.4 34.3z\' class=\'\'></path></svg></div>",
            rangeIcon: "<div style=\'width:20px\'><svg aria-hidden=\'true\' focusable=\'false\' data-prefix=\'fas\' data-icon=\'retweet\' role=\'img\' xmlns=\'http://www.w3.org/2000/svg\' viewBox=\'0 0 640 512\' class=\'svg-inline--fa fa-retweet fa-w-20\'><path fill=\'currentColor\' d=\'M629.657 343.598L528.971 444.284c-9.373 9.372-24.568 9.372-33.941 0L394.343 343.598c-9.373-9.373-9.373-24.569 0-33.941l10.823-10.823c9.562-9.562 25.133-9.34 34.419.492L480 342.118V160H292.451a24.005 24.005 0 0 1-16.971-7.029l-16-16C244.361 121.851 255.069 96 276.451 96H520c13.255 0 24 10.745 24 24v222.118l40.416-42.792c9.285-9.831 24.856-10.054 34.419-.492l10.823 10.823c9.372 9.372 9.372 24.569-.001 33.941zm-265.138 15.431A23.999 23.999 0 0 0 347.548 352H160V169.881l40.416 42.792c9.286 9.831 24.856 10.054 34.419.491l10.822-10.822c9.373-9.373 9.373-24.569 0-33.941L144.971 67.716c-9.373-9.373-24.569-9.373-33.941 0L10.343 168.402c-9.373 9.373-9.373 24.569 0 33.941l10.822 10.822c9.562 9.562 25.133 9.34 34.419-.491L96 169.881V392c0 13.255 10.745 24 24 24h243.549c21.382 0 32.09-25.851 16.971-40.971l-16.001-16z\' class=\'\'></path></svg></div>",
            headerSeparator: "<div style=\'width:20px\'><svg aria-hidden=\'true\' focusable=\'false\' data-prefix=\'fas\' data-icon=\'chevron-right\' role=\'img\' xmlns=\'http://www.w3.org/2000/svg\' viewBox=\'0 0 320 512\' class=\'svg-inline--fa fa-chevron-right fa-w-10\'><path fill=\'currentColor\' d=\'M285.476 272.971L91.132 467.314c-9.373 9.373-24.569 9.373-33.941 0l-22.667-22.667c-9.357-9.357-9.375-24.522-.04-33.901L188.505 256 34.484 101.255c-9.335-9.379-9.317-24.544.04-33.901l22.667-22.667c9.373-9.373 24.569-9.373 33.941 0L285.475 239.03c9.373 9.372 9.373 24.568.001 33.941z\' class=\'\'></path></svg></div>",
            oninit: function(caleran){
                if (!initialFrom || !initialTo) {
                    $("#MyBookingsRES-search-box-dates").html(defaultDatePickerContent);
                } else {
                    $("#MyBookingsRES-search-box-dates").html(
                        '<img src="'+ getIconUrl("calendar") +'" alt="calendar">' +
                        '<div id="MyBookingsRES-search-box-from">'+ initialFrom +'</div>' +
                        '<div style="margin-bottom: 3px;">&rarr;</div>' +
                        '<div id="MyBookingsRES-search-box-to">'+ initialTo
                        +'</div>'
                    );
                }
            },
            onaftershow: function(caleran){
                $("#MyBookingsRES-search-box-dates").html(defaultDatePickerContent);
            },
            onfirstselect: function (caleran, startDate) {
                caleranStartDate = formatDateToGerman(startDate._d);
                $("#MyBookingsRES-search-box-from").html(caleranStartDate);
            },
            onafterselect: function (caleran, startDate, endDate) {
                caleranEndDate = formatDateToGerman(endDate._d);
                $("#MyBookingsRES-search-box-to").html(caleranEndDate);
            },
            onafterhide: function (caleran) {
                let content = '';

                if (caleranStartDate && caleranEndDate) {

                    content = '<img src="'+ getIconUrl("calendar") +'" alt="calendar">' +
                        '<div id="MyBookingsRES-search-box-from">'+ caleranStartDate +'</div>' +
                        '<div style="margin-bottom: 3px;">&rarr;</div>' +
                        '<div id="MyBookingsRES-search-box-to">'+ caleranEndDate +'</div>'
                } else {
                    content = defaultDatePickerContent
                }

                $("#MyBookingsRES-search-box-dates").html(content);
            },
        });

        const realignDropDownMenu = function() {
            let windowWitdh = $(window).width();
            let dropDownParentWidth = $(".MyBookingsRES-search-item.MyBookingsRES-search-box-people-field-onclick").width();
            let margin = 8;

            if (windowWitdh < 1200) margin = 5;
            if (windowWitdh < 960) margin = 3;
            if (windowWitdh < 768) margin = -8;
            if (windowWitdh < 480) margin = -10;

            $("#MyBookingsRES-search-box-people-dropdown").css(
                'left',
                '-' + (dropDownParentWidth - margin).toFixed() + 'px'
            );
        };
        realignDropDownMenu();
        $(window).resize(function () {
            realignDropDownMenu();
        });

        $("#MyBookingsRES-search-box-people").click(function (e) {
            $("#MyBookingsRES-search-box-people-dropdown").css('display', 'block');
        });

        $("#MyBookingsRES-search-box-adults-minus").click(function (e) {
            let adultCount = parseInt($("#MyBookingsRES-search-box-adults-result").html());
            if (adultCount !== 1) {
                $("#MyBookingsRES-search-box-adults-result").html(adultCount - 1);
                $("#MyBookingsRES-search-box-adults").html(adultCount - 1);
            }
        });
        $("#MyBookingsRES-search-box-adults-plus").click(function (e) {
            let adultCount = parseInt($("#MyBookingsRES-search-box-adults-result").html());
            $("#MyBookingsRES-search-box-adults-result").html(adultCount + 1);
            $("#MyBookingsRES-search-box-adults").html(adultCount + 1);
        });

        $("#MyBookingsRES-search-box-children-minus").click(function (e) {
            let childrenCount = parseInt($("#MyBookingsRES-search-box-children-result").html());
            let finalChildrenCount = childrenCount - 1;
            if (childrenCount !== 0) {
                $("#MyBookingsRES-search-box-children-result").html(finalChildrenCount);
                $("#MyBookingsRES-search-box-children").html(finalChildrenCount);

                $("#MyBookingsRES-child-" + childrenCount).parent().remove();
            }
        });
        $("#MyBookingsRES-search-box-children-plus").click(function (e) {
            let childrenCount = parseInt($("#MyBookingsRES-search-box-children-result").html());
            let finalChildrenCount = childrenCount + 1;
            $("#MyBookingsRES-search-box-children-result").html(finalChildrenCount);
            $("#MyBookingsRES-search-box-children").html(finalChildrenCount);

            $(".MyBookingsRES-search-box-children-ages").append(
                '<div>' +
                    '<label for="MyBookingsRES-child-'+ finalChildrenCount +'">'
                    +   (lang === 'de' ? 'Kind ' + finalChildrenCount + ' alter' : 'Child '+ finalChildrenCount +' age') +
                    '</label>' +
                    '<select id="MyBookingsRES-child-'+ finalChildrenCount +'">' +
                        '<option value="1">1 '+ yearOldLabel +'</option>' +
                        '<option value="2">2 '+ yearsOldLabel +'</option>' +
                        '<option value="3">3 '+ yearsOldLabel +'</option>' +
                        '<option value="4">4 '+ yearsOldLabel +'</option>' +
                        '<option value="5">5 '+ yearsOldLabel +'</option>' +
                        '<option value="6">6 '+ yearsOldLabel +'</option>' +
                        '<option value="7">7 '+ yearsOldLabel +'</option>' +
                        '<option value="8">8 '+ yearsOldLabel +'</option>' +
                        '<option value="9">9 '+ yearsOldLabel +'</option>' +
                        '<option value="10">10 '+ yearsOldLabel +'</option>' +
                        '<option value="11">11 '+ yearsOldLabel +'</option>' +
                        '<option value="12">12 '+ yearsOldLabel +'</option>' +
                        '<option value="13">13 '+ yearsOldLabel +'</option>' +
                        '<option value="14">14 '+ yearsOldLabel +'</option>' +
                        '<option value="15">15 '+ yearsOldLabel +'</option>' +
                        '<option value="16">16 '+ yearsOldLabel +'</option>' +
                        '<option value="17">17 '+ yearsOldLabel +'</option>' +
                    '</select>' +
                '</div>'
            );
        });

        $("#MyBookingsRES-search-box-guests-minus").click(function (e) {
            let guestCount = parseInt($("#MyBookingsRES-search-box-guests-result").html());
            if (guestCount !== 1) {
                $("#MyBookingsRES-search-box-guests-result").html(guestCount - 1);
                $("#MyBookingsRES-search-box-adults").html(guestCount - 1);
            }
        });
        $("#MyBookingsRES-search-box-guests-plus").click(function (e) {
            let guestCount = parseInt($("#MyBookingsRES-search-box-guests-result").html());
            $("#MyBookingsRES-search-box-guests-result").html(guestCount + 1);
            $("#MyBookingsRES-search-box-adults").html(guestCount + 1);
        });


        $("#MyBookingsRES-search-submit").click(function (e) {
            $.ajax ({
                method: 'post',
                url: ajax.url,
                dataType: "json",
                data: {
                    action: 'MyBookingsRES_getResultPageUrl'
                },
                success: function (d) {
                    let resultPageUrl = d.data;

                    let area = $("#MyBookingsRES-search-container-region").val();
                    let guestCount = parseInt($("#MyBookingsRES-search-box-guests-result").html());
                    let adultCount = parseInt($("#MyBookingsRES-search-box-adults-result").html());
                    let childrenCount = parseInt($("#MyBookingsRES-search-box-children-result").html());
                    let childrenAges = [];

                    $('[id^=MyBookingsRES-child-]').each(function () {
                        childrenAges.push(parseInt($(this).val()));
                    });

                    /*
                    console.log({
                        area,
                        from: caleranStartDate,
                        to: caleranEndDate,
                        adultCount,
                        childrenCount,
                        childrenAges
                    });
                     */

                    if (!caleranStartDate || !caleranEndDate) {
                        return;
                    }

                    // Return if one of the dates contains no numbers
                    if (!/\d/.test(caleranStartDate) || !/\d/.test(caleranEndDate)) {
                        return;
                    }

                    let finalUrl = '';

                    if (isNaN(guestCount)) {
                        finalUrl = resultPageUrl +
                            "?mba=" + (area ? area : 'null') +
                            "&mbf=" + formatGermanDateStringToISO(caleranStartDate) +
                            "&mbt=" + formatGermanDateStringToISO(caleranEndDate) +
                            "&mbp=" + (adultCount + childrenCount) +
                            "&mbc=" + childrenCount;

                        if (childrenAges.length !== 0) {
                            finalUrl += "&mbca=" + childrenAges.join(',');
                        }
                    } else {
                        finalUrl = resultPageUrl +
                            "?mba=" + (area ? area : 'null') +
                            "&mbf=" + formatGermanDateStringToISO(caleranStartDate) +
                            "&mbt=" + formatGermanDateStringToISO(caleranEndDate) +
                            "&mbp=" + guestCount +
                            "&mbc=0";
                    }

                    window.location = encodeURI(finalUrl);
                },
                error: function() {
                    //any error to be handled
                }
            });
        });

        const formatDateToGerman = function(date) {
            let month = '' + (date.getMonth()+1);
            let day = '' + date.getDate();
            let year = date.getFullYear();

            if (month.length < 2) month = '0' + month;
            if (day.length < 2) day = '0' + day;

            return day + '.' + month + '.' + year;
        };

        const formatGermanDateStringToISO = function(date) {
            let dateUnits = date.split('.');

            return dateUnits[2] + '-' + dateUnits[1] + '-' + dateUnits[0]
        }
    };

    MyBookingsRESFrontent.initSearchShortCodeBG = function() {

        let lang = document.getElementsByTagName('html')[0].getAttribute('lang').substr(0, 2);

        let yearOldLabel = lang === 'de' ? 'jahr alt' : 'year old';
        let yearsOldLabel = lang === 'de' ? 'jahre alt' : 'years old';

        const getIconUrl = function(name) {
            let fullUrl = window.location;
            return fullUrl.protocol + "//" + fullUrl.host + "/wp-content/plugins/my-bookings-res/includes/media/icons/" + name + ".svg";
        };

        const fetchInputsOverwritten = function () {
            var elValue = null;
            if ($.inArray(this.config.target.get(0).tagName, this.globals.valElements) !== -1) {
                elValue = this.config.target.val();
            } else {
                if (this.config && this.config.startDate && this.config.startDate._d) {
                    let currentDate = this.config.startDate._d;
                    let month = '' + (currentDate.getMonth()+1);
                    let day = '' + currentDate.getDate();
                    let year = currentDate.getFullYear();

                    if (month.length < 2) month = '0' + month;
                    if (day.length < 2) day = '0' + day;

                    elValue = day + '.' + month + '.' + year;
                } else {
                    elValue = '';
                }
            }
            if (this.config.singleDate === false && elValue.indexOf(this.config.dateSeparator) > 0) {
                var parts = elValue.split(this.config.dateSeparator);
                if (parts.length == 2) {
                    if (moment(parts[0], this.config.format, this.config.locale).isValid() && moment(parts[1], this.config.format, this.config.locale).isValid()) {
                        this.config.startDate = moment(parts[0], this.config.format, this.config.locale).middleOfDay();
                        this.config.endDate = moment(parts[1], this.config.format, this.config.locale).middleOfDay();
                        this.globals.firstValueSelected = true;
                    }
                }
            } else if (this.config.singleDate === true) {
                var value = elValue;
                if (value != "" && moment(value, this.config.format, this.config.locale).isValid()) {
                    this.config.startDate = moment(value, this.config.format, this.config.locale).middleOfDay();
                    this.config.endDate = moment(value, this.config.format, this.config.locale).middleOfDay();
                    this.globals.firstValueSelected = true;
                }
            }// clear input if startEmpty is defined
            if (this.config.startEmpty && !this.globals.firstValueSelected) {
                this.clearInput();
            }
            // validate inputs
            this.validateDates();
        };

        let germanMonthNames = [
            'Jän', 'Feb', 'Mrz', 'Apr', 'Mai', 'Jun', 'Jul', 'Aug', 'Sep', 'Okt', 'Nov', 'Dez'
        ];

        let initialFrom = new Date();
        let initialFromDay = initialFrom.getDate();
        let initialFromMonth = initialFrom.getMonth() + 1;

        let initialTo = new Date((new Date()).setDate(initialFrom.getDate() + 7));
        let initialToDay = initialTo.getDate();
        let initialToMonth = initialTo.getMonth() + 1;

        let fromDate = initialFrom;
        let fromDay = initialFromDay;
        let fromMonth = initialFromMonth;
        let toDate = initialTo;
        let toDay = initialToDay;
        let toMonth = initialToMonth;

        const defaultDatePickerContentFrom = function() {
            return '<label>CHECK-IN</label>' +
                   '<div class="MyBookingsRES-search-box-bg-date-picker">' +
                   '    <div id="MyBookingsRES-search-box-bg-checkin-day">'+ fromDay +'</div>' +
                   '    <div>' +
                   '        <div id="MyBookingsRES-search-box-bg-checkin-month">'+ germanMonthNames[fromMonth - 1] +'</div>' +
                   '        <div>' +
                   '            <img src="'+ getIconUrl("chevron-down") +'" alt="chevron-down">' +
                   '        </div>' +
                   '    </div>' +
                   '</div>';
        };

        const defaultDatePickerContentTo = function () {
            return '<label>CHECK-OUT</label>' +
                    '<div class="MyBookingsRES-search-box-bg-date-picker">' +
                    '    <div id="MyBookingsRES-search-box-bg-checkout-day">'+ toDay +'</div>' +
                    '    <div>' +
                    '        <div id="MyBookingsRES-search-box-bg-checkout-month">'+ germanMonthNames[toMonth - 1] +'</div>' +
                    '        <div>' +
                    '           <img src="'+ getIconUrl("chevron-down") +'" alt="chevron-down">' +
                    '        </div>' +
                    '    </div>' +
                    '</div>';
        };

        let caleranInstanceBGFrom = $(".MyBookingsRES-search-box-bg-date-from").caleran({
            singleDate: true,
            showFooter: false,
            format: "DD.MM.YYYY",
            autoCloseOnSelect: true,
            startEmpty: true,
            nextMonthIcon: "<div style=\'width:20px\'><svg aria-hidden=\'true\' focusable=\'false\' data-prefix=\'fas\' data-icon=\'arrow-right\' role=\'img\' xmlns=\'http://www.w3.org/2000/svg\' viewBox=\'0 0 448 512\' class=\'svg-inline--fa fa-arrow-right fa-w-14\'><path fill=\'currentColor\' d=\'M190.5 66.9l22.2-22.2c9.4-9.4 24.6-9.4 33.9 0L441 239c9.4 9.4 9.4 24.6 0 33.9L246.6 467.3c-9.4 9.4-24.6 9.4-33.9 0l-22.2-22.2c-9.5-9.5-9.3-25 .4-34.3L311.4 296H24c-13.3 0-24-10.7-24-24v-32c0-13.3 10.7-24 24-24h287.4L190.9 101.2c-9.8-9.3-10-24.8-.4-34.3z\' class=\'\'></path></svg></div>",
            prevMonthIcon: "<div style=\'width:20px\'><svg aria-hidden=\'true\' focusable=\'false\' data-prefix=\'fas\' data-icon=\'arrow-left\' role=\'img\' xmlns=\'http://www.w3.org/2000/svg\' viewBox=\'0 0 448 512\' class=\'svg-inline--fa fa-arrow-left fa-w-14\'><path fill=\'currentColor\' d=\'M257.5 445.1l-22.2 22.2c-9.4 9.4-24.6 9.4-33.9 0L7 273c-9.4-9.4-9.4-24.6 0-33.9L201.4 44.7c9.4-9.4 24.6-9.4 33.9 0l22.2 22.2c9.5 9.5 9.3 25-.4 34.3L136.6 216H424c13.3 0 24 10.7 24 24v32c0 13.3-10.7 24-24 24H136.6l120.5 114.8c9.8 9.3 10 24.8.4 34.3z\' class=\'\'></path></svg></div>",
            headerSeparator: "<div style=\'width:20px\'><svg aria-hidden=\'true\' focusable=\'false\' data-prefix=\'fas\' data-icon=\'chevron-right\' role=\'img\' xmlns=\'http://www.w3.org/2000/svg\' viewBox=\'0 0 320 512\' class=\'svg-inline--fa fa-chevron-right fa-w-10\'><path fill=\'currentColor\' d=\'M285.476 272.971L91.132 467.314c-9.373 9.373-24.569 9.373-33.941 0l-22.667-22.667c-9.357-9.357-9.375-24.522-.04-33.901L188.505 256 34.484 101.255c-9.335-9.379-9.317-24.544.04-33.901l22.667-22.667c9.373-9.373 24.569-9.373 33.941 0L285.475 239.03c9.373 9.372 9.373 24.568.001 33.941z\' class=\'\'></path></svg></div>",
            oninit: function(caleran){
                $(".MyBookingsRES-search-box-bg-date-from").html(defaultDatePickerContentFrom());
            },
            onaftershow: function(caleran){
                $(".MyBookingsRES-search-box-bg-date-from").html(defaultDatePickerContentFrom());
            },
            onafterselect: function (caleran, startDate, endDate) {
                fromDate = endDate._d;
                fromDay = fromDate.getDate();
                fromMonth = fromDate.getMonth() + 1;
                $("#MyBookingsRES-search-box-bg-checkin-day").html(fromDay);
                $("#MyBookingsRES-search-box-bg-checkin-month").html(germanMonthNames[fromMonth - 1]);

                caleran.globals.currentDate._i = formatDateToGerman(fromDate);
                caleran.globals.startDateInitial._i = formatDateToGerman(fromDate);
                caleran.globals.endDateInitial._i = formatDateToGerman(fromDate);
                caleran.fetchInputs();

                toDate = new Date(JSON.parse(JSON.stringify(fromDate)));
                toDate.setDate(toDate.getDate() + 2);
                toDay = toDate.getDate();
                toMonth = toDate.getMonth() + 1;
                $("#MyBookingsRES-search-box-bg-checkout-day").html(toDay);
                $("#MyBookingsRES-search-box-bg-checkout-month").html(germanMonthNames[toMonth - 1]);
            },
            onafterhide: function (caleran) {
                $(".MyBookingsRES-search-box-bg-date-from").html(defaultDatePickerContentFrom());
            },
        });
        if (caleranInstanceBGFrom.data('caleran')) {
            caleranInstanceBGFrom.data('caleran').__proto__.fetchInputs = fetchInputsOverwritten;
            caleranInstanceBGFrom.data('caleran').init();
        }

        let caleranInstanceBGTo = $(".MyBookingsRES-search-box-bg-date-to").caleran({
            singleDate: true,
            showFooter: false,
            format: "DD.MM.YYYY",
            autoCloseOnSelect: true,
            startEmpty: true,
            nextMonthIcon: "<div style=\'width:20px\'><svg aria-hidden=\'true\' focusable=\'false\' data-prefix=\'fas\' data-icon=\'arrow-right\' role=\'img\' xmlns=\'http://www.w3.org/2000/svg\' viewBox=\'0 0 448 512\' class=\'svg-inline--fa fa-arrow-right fa-w-14\'><path fill=\'currentColor\' d=\'M190.5 66.9l22.2-22.2c9.4-9.4 24.6-9.4 33.9 0L441 239c9.4 9.4 9.4 24.6 0 33.9L246.6 467.3c-9.4 9.4-24.6 9.4-33.9 0l-22.2-22.2c-9.5-9.5-9.3-25 .4-34.3L311.4 296H24c-13.3 0-24-10.7-24-24v-32c0-13.3 10.7-24 24-24h287.4L190.9 101.2c-9.8-9.3-10-24.8-.4-34.3z\' class=\'\'></path></svg></div>",
            prevMonthIcon: "<div style=\'width:20px\'><svg aria-hidden=\'true\' focusable=\'false\' data-prefix=\'fas\' data-icon=\'arrow-left\' role=\'img\' xmlns=\'http://www.w3.org/2000/svg\' viewBox=\'0 0 448 512\' class=\'svg-inline--fa fa-arrow-left fa-w-14\'><path fill=\'currentColor\' d=\'M257.5 445.1l-22.2 22.2c-9.4 9.4-24.6 9.4-33.9 0L7 273c-9.4-9.4-9.4-24.6 0-33.9L201.4 44.7c9.4-9.4 24.6-9.4 33.9 0l22.2 22.2c9.5 9.5 9.3 25-.4 34.3L136.6 216H424c13.3 0 24 10.7 24 24v32c0 13.3-10.7 24-24 24H136.6l120.5 114.8c9.8 9.3 10 24.8.4 34.3z\' class=\'\'></path></svg></div>",
            headerSeparator: "<div style=\'width:20px\'><svg aria-hidden=\'true\' focusable=\'false\' data-prefix=\'fas\' data-icon=\'chevron-right\' role=\'img\' xmlns=\'http://www.w3.org/2000/svg\' viewBox=\'0 0 320 512\' class=\'svg-inline--fa fa-chevron-right fa-w-10\'><path fill=\'currentColor\' d=\'M285.476 272.971L91.132 467.314c-9.373 9.373-24.569 9.373-33.941 0l-22.667-22.667c-9.357-9.357-9.375-24.522-.04-33.901L188.505 256 34.484 101.255c-9.335-9.379-9.317-24.544.04-33.901l22.667-22.667c9.373-9.373 24.569-9.373 33.941 0L285.475 239.03c9.373 9.372 9.373 24.568.001 33.941z\' class=\'\'></path></svg></div>",
            oninit: function(caleran){
                $(".MyBookingsRES-search-box-bg-date-to").html(defaultDatePickerContentTo());
            },
            onaftershow: function(caleran){
                $(".MyBookingsRES-search-box-bg-date-to").html(defaultDatePickerContentTo());
            },
            onafterselect: function (caleran, startDate, endDate) {
                toDate = endDate._d;
                toDay = toDate.getDate();
                toMonth = toDate.getMonth() + 1;
                $("#MyBookingsRES-search-box-bg-checkout-day").html(toDay);
                $("#MyBookingsRES-search-box-bg-checkout-month").html(germanMonthNames[toMonth - 1]);

                caleran.globals.currentDate._i = formatDateToGerman(toDate);
                caleran.globals.startDateInitial._i = formatDateToGerman(toDate);
                caleran.globals.endDateInitial._i = formatDateToGerman(toDate);
                caleran.fetchInputs();
            },
            onafterhide: function (caleran) {
                $(".MyBookingsRES-search-box-bg-date-to").html(defaultDatePickerContentTo());
            },
        });

        if (caleranInstanceBGTo.data('caleran') !== undefined) {
            caleranInstanceBGTo.data('caleran').__proto__.fetchInputs = fetchInputsOverwritten;
            caleranInstanceBGTo.data('caleran').init();
        }

        $(".MyBookingsRES-search-box-bg-people-onclick").click(function (e) {
            $("#MyBookingsRES-search-box-bg-people-dropdown").css("display", "block");
        });

        $("#MyBookingsRES-search-box-bg-adults-minus").click(function (e) {
            let adultCount = parseInt($("#MyBookingsRES-search-box-bg-adults-result").html());
            if (adultCount !== 1) {
                $("#MyBookingsRES-search-box-bg-adults-result").html(adultCount - 1);
                $("#MyBookingsRES-search-box-bg-adults").html(adultCount - 1);
            }
        });
        $("#MyBookingsRES-search-box-bg-adults-plus").click(function (e) {
            let adultCount = parseInt($("#MyBookingsRES-search-box-bg-adults-result").html());
            $("#MyBookingsRES-search-box-bg-adults-result").html(adultCount + 1);
            $("#MyBookingsRES-search-box-bg-adults").html(adultCount + 1);
        });

        $("#MyBookingsRES-search-box-bg-children-minus").click(function (e) {
            let childrenCount = parseInt($("#MyBookingsRES-search-box-bg-children-result").html());
            let finalChildrenCount = childrenCount - 1;
            if (childrenCount !== 0) {
                $("#MyBookingsRES-search-box-bg-children-result").html(finalChildrenCount);
                $("#MyBookingsRES-search-box-bg-children").html(finalChildrenCount);

                $("#MyBookingsRES-child-" + childrenCount).parent().remove();
            }
        });
        $("#MyBookingsRES-search-box-bg-children-plus").click(function (e) {
            let childrenCount = parseInt($("#MyBookingsRES-search-box-bg-children-result").html());
            let finalChildrenCount = childrenCount + 1;
            $("#MyBookingsRES-search-box-bg-children-result").html(finalChildrenCount);
            $("#MyBookingsRES-search-box-bg-children").html(finalChildrenCount);

            $(".MyBookingsRES-search-box-bg-children-ages").append(
                '<div>' +
                '<label for="MyBookingsRES-child-'+ finalChildrenCount +'">'
                +   (lang === 'de' ? 'Kind ' + finalChildrenCount + ' alter' : 'Child '+ finalChildrenCount +' age') +
                '</label>' +
                '<select id="MyBookingsRES-child-'+ finalChildrenCount +'">' +
                '<option value="1">1 '+ yearOldLabel +'</option>' +
                '<option value="2">2 '+ yearsOldLabel +'</option>' +
                '<option value="3">3 '+ yearsOldLabel +'</option>' +
                '<option value="4">4 '+ yearsOldLabel +'</option>' +
                '<option value="5">5 '+ yearsOldLabel +'</option>' +
                '<option value="6">6 '+ yearsOldLabel +'</option>' +
                '<option value="7">7 '+ yearsOldLabel +'</option>' +
                '<option value="8">8 '+ yearsOldLabel +'</option>' +
                '<option value="9">9 '+ yearsOldLabel +'</option>' +
                '<option value="10">10 '+ yearsOldLabel +'</option>' +
                '<option value="11">11 '+ yearsOldLabel +'</option>' +
                '<option value="12">12 '+ yearsOldLabel +'</option>' +
                '<option value="13">13 '+ yearsOldLabel +'</option>' +
                '<option value="14">14 '+ yearsOldLabel +'</option>' +
                '<option value="15">15 '+ yearsOldLabel +'</option>' +
                '<option value="16">16 '+ yearsOldLabel +'</option>' +
                '<option value="17">17 '+ yearsOldLabel +'</option>' +
                '</select>' +
                '</div>'
            );
        });

        $("#MyBookingsRES-search-box-bg-guests-minus").click(function (e) {
            let guestCount = parseInt($("#MyBookingsRES-search-box-bg-guests-result").html());
            if (guestCount !== 1) {
                $("#MyBookingsRES-search-box-bg-guests-result").html(guestCount - 1);
            }
        });
        $("#MyBookingsRES-search-box-bg-guests-plus").click(function (e) {
            let guestCount = parseInt($("#MyBookingsRES-search-box-bg-guests-result").html());
            $("#MyBookingsRES-search-box-bg-guests-result").html(guestCount + 1);
        });

        $("#MyBookingsRES-search-submit-bg").click(function (e) {
            $.ajax ({
                method: 'post',
                url: ajax.url,
                dataType: "json",
                data: {
                    action: 'MyBookingsRES_getResultPageUrl'
                },
                success: function (d) {
                    let resultPageUrl = d.data;

                    let area = $("#MyBookingsRES-search-container-bg-region").val();
                    let guestCount = parseInt($("#MyBookingsRES-search-box-bg-guests-result").html());
                    let adultCount = parseInt($("#MyBookingsRES-search-box-bg-adults-result").html());
                    let childrenCount = parseInt($("#MyBookingsRES-search-box-bg-children-result").html());
                    let childrenAges = [];

                    $('[id^=MyBookingsRES-child-]').each(function () {
                        childrenAges.push(parseInt($(this).val()));
                    });

                    /*
                    console.log({
                        area,
                        from: formatDateToGerman(fromDate),
                        to: formatDateToGerman(toDate),
                        adultCount,
                        childrenCount,
                        childrenAges
                    });
                     */

                    let finalUrl = '';

                    if (isNaN(guestCount)) {

                        finalUrl = resultPageUrl +
                            "?mba=" + (area ? area : 'null') +
                            "&mbf=" + formatDateToISO(fromDate) +
                            "&mbt=" + formatDateToISO(toDate) +
                            "&mbp=" + (adultCount + childrenCount) +
                            "&mbc=" + childrenCount;

                        if (childrenAges.length !== 0) {
                            finalUrl += "&mbca=" + childrenAges.join(',');
                        }

                    } else {

                        finalUrl = resultPageUrl +
                            "?mba=" + (area ? area : 'null') +
                            "&mbf=" + formatDateToISO(fromDate) +
                            "&mbt=" + formatDateToISO(toDate) +
                            "&mbp=" + guestCount +
                            "&mbc=0";

                    }

                    window.location = encodeURI(finalUrl);
                },
                error: function() {
                    //any error to be handled
                }
            });
        });

        const formatDateToGerman = function(date) {
            let month = '' + (date.getMonth()+1);
            let day = '' + date.getDate();
            let year = date.getFullYear();

            if (month.length < 2) month = '0' + month;
            if (day.length < 2) day = '0' + day;

            return day + '.' + month + '.' + year;
        };

        const formatDateToISO = function(date) {
            let month = '' + (date.getMonth()+1);
            let day = '' + date.getDate();
            let year = date.getFullYear();

            if (month.length < 2) month = '0' + month;
            if (day.length < 2) day = '0' + day;

            return year + '-' + month + '-' + day;
        };
    };

    MyBookingsRESFrontent.initSearchShortCodes = function() {
        MyBookingsRESFrontent.initSearchShortCode();
        MyBookingsRESFrontent.initSearchShortCodeBG();

        window.onclick = function(event){

            let searchSM = document.getElementsByClassName('MyBookingsRES-search-box-bg-people-onclick')[0];
            let searchBG = document.getElementsByClassName('MyBookingsRES-search-box-people-field-onclick')[0];

            if (searchSM && !searchSM.contains(event.target)){
                $("#MyBookingsRES-search-box-bg-people-dropdown").css('display', 'none');
            }

            if (searchBG && !searchBG.contains(event.target)){
                $("#MyBookingsRES-search-box-people-dropdown").css('display', 'none');
            }
        };
    };

    MyBookingsRESFrontent.resultShortCode = {
        activeRequest: null,
        checkedAttributeIds: [],
        checkedLocationIds: [],
        init: function() {
            $("#MyBookingsRES_filter_button").click(function (e) {
                $(".mybookingsres-filter-container-mobile").toggle();
            });

            MyBookingsRESFrontent.resultShortCode.checkLocationsFromUrlParameters();

            $("input[id^='mybookingsres-filter-attribute-']").change(function (e) {
                MyBookingsRESFrontent.resultShortCode.filterOnChange();
                MyBookingsRESFrontent.resultShortCode.updateParameters();
                MyBookingsRESFrontent.resultShortCode.loadFilteredCategories();
            });

            $("input[id^='mybookingsres-filter-location-']").change(function (e) {
                MyBookingsRESFrontent.resultShortCode.filterOnChange();
                MyBookingsRESFrontent.resultShortCode.updateParameters();
                MyBookingsRESFrontent.resultShortCode.loadFilteredCategories();
            });
        },
        filterOnChange: function() {
            let mobileView = !$('.mybookingsres-filter-container').is(':visible');

            let selectorString = ".mybookingsres-filter-container input[id^='mybookingsres-filter-location-']:checked";

            if (mobileView) {
                selectorString = ".mybookingsres-filter-container-mobile input[id^='mybookingsres-filter-location-']:checked";
            }

            MyBookingsRESFrontent.resultShortCode.checkedAttributeIds = [];
            $("input[id^='mybookingsres-filter-attribute-']:checked").each(function (e) {
                let elementId = $(this).attr('id');
                let attributeId = parseInt(elementId.replace('mybookingsres-filter-attribute-', ''));

                MyBookingsRESFrontent.resultShortCode.checkedAttributeIds.push(attributeId);
            });

            MyBookingsRESFrontent.resultShortCode.checkedLocationIds = [];
            $(selectorString).each(function (checked) {
                let elementId = $(this).attr('id');
                let locationId = parseInt(elementId.replace('mybookingsres-filter-location-', ''));

                MyBookingsRESFrontent.resultShortCode.checkedLocationIds.push(locationId);
            });
        },
        loadFilteredCategories: function() {
            if (MyBookingsRESFrontent.resultShortCode.activeRequest) {
                MyBookingsRESFrontent.resultShortCode.activeRequest.abort();
            }

            const requestData = {
                action: 'MyBookingsRES_getFilteredCategories',
                attributeIds: MyBookingsRESFrontent.resultShortCode.checkedAttributeIds,
                locationIds: MyBookingsRESFrontent.resultShortCode.checkedLocationIds
            };

            MyBookingsRESFrontent.resultShortCode.activeRequest =
                MyBookingsRESFrontent.webRequest(requestData, function (response) {
                if (response && response.error === 0) {
                    let categoryIds = response.data;

                    $(".mybookingsres-list-item").hide();

                    for(let i = 0; i < categoryIds.length; i++) {
                        $('.mybookingsres-list-item[data-category="' + categoryIds[i] + '"]').show();
                    }
                }
            });
        },
        updateParameters: function() {
            const url = new URL(window.location);

            if (MyBookingsRESFrontent.resultShortCode.checkedLocationIds.length > 0) {
                url.searchParams.set('mbl', MyBookingsRESFrontent.resultShortCode.checkedLocationIds.join(','));
            } else {
                url.searchParams.delete('mbl');
            }

            window.history.pushState({}, '', url);
        },
        checkLocationsFromUrlParameters: function() {

            const urlParams = new URLSearchParams(window.location.search);

            let mbl = urlParams.get('mbl');

            let locationIdsToCheck = [];
            if (mbl !== null) {
                locationIdsToCheck = mbl.split(',');
            }

            for (let i = 0; i < locationIdsToCheck.length; i++) {
                $("input[id='mybookingsres-filter-location-" + locationIdsToCheck[i] + "']").prop('checked', true);

                MyBookingsRESFrontent.resultShortCode.checkedLocationIds.push(locationIdsToCheck[i]);
            }

            MyBookingsRESFrontent.resultShortCode.loadFilteredCategories();
        }
    };

    $(function() {

        MyBookingsRESFrontent.initSearchShortCodes();

        MyBookingsRESFrontent.resultShortCode.init();

        $("#MyBookingsRES-searchBookings").click(function (e) {

            $.ajax ({
                method: 'post',
                url: ajax.url,
                dataType: "json",
                data: {
                    action: 'MyBookingsRES_getApartmentURL'
                },
                success: function (d) {
                    //let selectedRegion = $("#MyBookingsRES-selectedRegion").val();
                    let selectedRegion = "1417164160";
                    let personCount = $("#MyBookingsRES-selectedPersonCount").val();
                    let selectedBookingDateRangeInput = $("#MyBookingsRES-selectedBookingDateRangeInput").val();
                    let selectedBookingDateRangeInputArray = selectedBookingDateRangeInput.split(' – ');
                    let from = selectedBookingDateRangeInputArray[0];
                    let to = selectedBookingDateRangeInputArray[1];

                    let currentURL = window.location;

                    let apartmentURL = d.data;

                    console.log('test', apartmentURL);

                    window.location = apartmentURL + "?f=" + from + "&t=" + to + "&people=" + personCount;
                },
                error: function() {
                    //any error to be handled
                }
            });

            e.preventDefault();
        });

        $(".MyBookingsRESFrontent_bookNowButton").click(function(e) {
            e.preventDefault();
            MyBookingsRESFrontent.List.bookNowButton_click($(this).data("category"), $(this).data("search") == '1' ? true : false);
        });

        $("#MyBookingsRESFrontent_List_button_search").click(function(e) {
            e.preventDefault();

            if (MyBookingsRESFrontent_searchParameters.calendarFrom != null &&
                    MyBookingsRESFrontent_searchParameters.calendarTo != null) {
                        
                MyBookingsRESFrontent_searchParameters.from = MyBookingsRESFrontent_searchParameters.calendarFrom;
                MyBookingsRESFrontent_searchParameters.to = MyBookingsRESFrontent_searchParameters.calendarTo;
                MyBookingsRESFrontent_searchParameters.people = $("#mybookingsres-list-searchbox-people").val();

                MyBookingsRESFrontent.List.reload(true);
                MyBookingsRESFrontent.List.startAvailabilityCheck();
            }

        });

        $("#MyBookingsRESFrontent_List_button_reset").click(function(e) {
            e.preventDefault();
            MyBookingsRESFrontent.List.reload(false);
        });

        $(".MyBookingsRESFrontent_List_submenu_button").click(function(e) {
            e.preventDefault();
            $('.'+$(this).data("container")).toggleClass('mybookingsres-dontshow');

            if ($(this).data("container") == "mybookingsres-list-map-container" && !$('.'+$(this).data("container")).hasClass("mybookingsres-dontshow")) {
                MyBookingsRESFrontent.List.GMap.updateViewport();
            }
        });

        const findGetParameter = function(parameterName) {
            let result = null,
                tmp = [];
            let items = location.search.substr(1).split("&");
            for (let index = 0; index < items.length; index++) {
                tmp = items[index].split("=");
                if (tmp[0] === parameterName) result = decodeURIComponent(tmp[1]);
            }
            return result;
        };
    });

})(jQuery);

/**
  * split array into packages
  * array.MyBookingsRESFrontent_chunk(10);
*/
Object.defineProperty(Array.prototype, "MyBookingsRESFrontent_chunk", {
    value: function(chunkSize) {
        let R = [];
        for (let i = 0; i < this.length; i += chunkSize) {
            R.push(this.slice(i, i + chunkSize));
        }
        return R;
    }
});