var MyBookingsRESAdmin = {};

(function($) {

    MyBookingsRESAdmin.saveSettings = function() {

        var apiKey = $("#MyBookingsRES-api-key").val(),
            websiteConfigId = $("#MyBookingsRES-website-config-id").val(),
            accommodationType = $("#MyBookingsRES-accommodation-type").val(),
            googleAPIKey = $("#MyBookingsRES-google-apikey").val(),
            color1 = $("#MyBookingsRES-color1").val(),
            color2 = $("#MyBookingsRES-color2").val(),
            color3 = $("#MyBookingsRES-color3").val(),
            color4 = $("#MyBookingsRES-color4").val(),
            color_bglist = $("#MyBookingsRES-color_bglist").val(),
            custom_css = $("#MyBookingsRES-custom_css").val(),
            showAreas = $("#MyBookingsRES-show-area-in-search").is(":checked"),
            hideChildrenAges = $("#MyBookingsRES-show-children-in-search").is(":checked"),
            sortCategories = $("#MyBookingsRES-sort-categories-by-sort-order").is(":checked"),
            showFilter = $("#MyBookingsRES-show-category-page-filter").is(":checked"),
            hideUnavailableCategories = $("#MyBookingsRES-hide-unavailable-categories").is(":checked");

        if (apiKey.length < 4 || accommodationType.length == 0 || websiteConfigId.length < 4) {
            alert("Bitte alle Fehler ausfüllen!");
            return;
        }

        $.ajax ({
            method: 'post',
            url: ajax.url,
            dataType: "json",
            data: {
                action: 'MyBookingsRES_saveSettings',
                apiKey: apiKey,
                accommodationType: accommodationType,
                googleAPIKey: googleAPIKey,
                color1: color1,
                color2: color2,
                color3: color3,
                color4: color4,
                color_bglist: color_bglist,
                websiteConfigId: websiteConfigId,
                custom_css: custom_css,
                showAreas: showAreas,
                hideChildrenAges: hideChildrenAges,
                sortCategories: sortCategories,
                showFilter: showFilter,
                hideUnavailableCategories: hideUnavailableCategories
            },
            success: function (d) {

                if (d.error === 0) {
                    window.location.href = location.href;
                }

            },
            error: function() {

            }
        });
    };

    
    /*
    MyBookingsRESAdmin.getCategoriesShortInfos = function() {

        MyBookingsRESAdmin.syncCategoryData.reset();

        $.ajax ({
            method: 'post',
            url: ajax.url,
            dataType: "json",
            data: {
                action: 'MyBookingsRES_getCategoriesShortInfos'    
            },
            success: function (d) {
                
                console.log(d.data);    

                if (d.error === 0) {
                    MyBookingsRESAdmin.syncCategoryData.categories = d.data;
                    MyBookingsRESAdmin.syncCategoryData.categoriesCount = d.data.length;
                    MyBookingsRESAdmin.syncCategoryData.go();                    
                }

            },
            error: function() {
                //any error to be handled
            }
        });
    };
*/
    MyBookingsRESAdmin.createCategoryPages = function() {
        MyBookingsRESAdmin.setLoader(true);
        $.ajax ({
            method: 'post',
            url: ajax.url,
            dataType: "json",
            data: {
                action: 'MyBookingsRES_createCategoryPages'
            },
            success: function (d) {
                MyBookingsRESAdmin.setLoader(false);
            },
            error: function() {
                //any error to be handled
            }
        });
    };
    MyBookingsRESAdmin.syncAreas = function() {
        MyBookingsRESAdmin.setLoader(true);
        $.ajax ({
            method: 'post',
            url: ajax.url,
            dataType: "json",
            data: {
                action: 'MyBookingsRES_syncAreas'
            },
            success: function (d) {
                MyBookingsRESAdmin.setLoader(false);
            },
            error: function() {
                //any error to be handled
            }
        });
    };
    MyBookingsRESAdmin.syncLocations = function() {
        MyBookingsRESAdmin.setLoader(true);
        $.ajax ({
            method: 'post',
            url: ajax.url,
            dataType: "json",
            data: {
                action: 'MyBookingsRES_syncLocations'
            },
            success: function (d) {
                MyBookingsRESAdmin.setLoader(false);
            },
            error: function() {
                //any error to be handled
            }
        });
    };

    MyBookingsRESAdmin.removeCategoryPages = function() {
        MyBookingsRESAdmin.setLoader(true);
        $.ajax ({
            method: 'post',
            url: ajax.url,
            dataType: "json",
            data: {
                action: 'MyBookingsRES_removeCategoryPages'
            },
            success: function (d) {
                MyBookingsRESAdmin.setLoader(false);
            },
            error: function() {
                //any error to be handled
            }
        });
    };

    MyBookingsRESAdmin.createResultPages = function() {
        MyBookingsRESAdmin.setLoader(true);
        $.ajax ({
            method: 'post',
            url: ajax.url,
            dataType: "json",
            data: {
                action: 'MyBookingsRES_createResultPages'
            },
            success: function (d) {
                MyBookingsRESAdmin.setLoader(false);
            },
            error: function() {
                //any error to be handled
            }
        });
    };

    MyBookingsRESAdmin.createPaymentReturnPages = function() {
        MyBookingsRESAdmin.setLoader(true);
        $.ajax ({
            method: 'post',
            url: ajax.url,
            dataType: "json",
            data: {
                action: 'MyBookingsRES_createPaymentReturnPages'
            },
            success: function (d) {
                MyBookingsRESAdmin.setLoader(false);
            },
            error: function() {
                //any error to be handled
            }
        });
    };

    MyBookingsRESAdmin.deleteUnusedCategories = function() {

        MyBookingsRESAdmin.syncCategoryData.reset();
        
        $.ajax ({
            method: 'post',
            url: ajax.url,
            dataType: "json",
            data: {
                action: 'MyBookingsRES_deleteUnusedCategories'
            },
            success: function (d) {

                if (d.error === 0) {
                    $("#MyBookingsRESAdmin_infoDeleteUnusedCategories .MyBookingsRESAdmin_isDone").show();

                    MyBookingsRESAdmin.syncCategoryData.categories = d.data;
                    MyBookingsRESAdmin.syncCategoryData.categoriesCount = d.data.length;
                    MyBookingsRESAdmin.syncCategoryData.syncPackage = MyBookingsRESAdmin.syncCategoryData.categories.MyBookingsRESFrontent_chunk(10);
                    MyBookingsRESAdmin.syncCategoryData.syncPackageCount = MyBookingsRESAdmin.syncCategoryData.syncPackage.length;
                    MyBookingsRESAdmin.syncCategoryData.go();
                }

            },
            error: function() {
                //any error to be handled
            }
        });
    };

    MyBookingsRESAdmin.syncCategoryData = {

        categories: null,
        categoriesCount: 0,
        currentIdx: -1,
        currentRequest: null,
        syncPackage: null, 
        syncPackageCount: 0, 

        reset : function() {
            if (MyBookingsRESAdmin.syncCategoryData.currentRequest != null) {
                MyBookingsRESAdmin.syncCategoryData.currentRequest.abort();
            }

            $("#MyBookingsRESAdmin_categoryStatus").html("");

            $("#MyBookingsRESAdmin_syncStoredCategories").hide();
            $("#MyBookingsRESAdmin_infoDeleteUnusedCategories .MyBookingsRESAdmin_isDone").hide();
            $("#MyBookingsRESAdmin_infoDeleteUnusedCategories").show();

            MyBookingsRESAdmin.syncCategoryData.currentRequest = null;
            MyBookingsRESAdmin.syncCategoryData.currentIdx = -1;      
            MyBookingsRESAdmin.syncCategoryData.categories = null;
            MyBookingsRESAdmin.syncCategoryData.categoriesCount = 0; 
        },

        go : function() {

            MyBookingsRESAdmin.syncCategoryData.currentIdx++;
            
            var currentCategories = MyBookingsRESAdmin.syncCategoryData.syncPackage[MyBookingsRESAdmin.syncCategoryData.currentIdx];
          
            console.log(currentCategories);

            var currentCategory, categoriesToLoad = [], rowHtml = [];
            for(var i in currentCategories) {
                currentCategory = currentCategories[i];
                categoriesToLoad.push(currentCategory.catid);

                rowHtml.push('<div class="mybookingsres-row mybookingsres-row1 category-' + currentCategory.catid + '">');
                rowHtml.push('    <div class="mybookingsres-col-8">' + currentCategory.catid + ' ' + currentCategory.ndesc + ' ...</div>');
                rowHtml.push('    <div class="mybookingsres-col-4"><span class="currentStatus">lade</span></div>');
                rowHtml.push('</div>');
            }
            $("#MyBookingsRESAdmin_categoryStatus").append(rowHtml.join(""));
            
            MyBookingsRESAdmin.syncCategoryData.currentRequest = $.ajax ({
                method: 'post',
                url: ajax.url,
                dataType: "json",
                data: {
                    action: 'MyBookingsRES_syncCategories',
                    categories: categoriesToLoad.join(",")
                },
                success: function (d) {
                    
                    console.log(d.data);    
        
                    if (d.error === 0) {

                        $("#MyBookingsRESAdmin_categoryStatus .category-").html("fertig");

                        for (var k in d.data){
                            if (d.data[k] == "OK") {
                                $("#MyBookingsRESAdmin_categoryStatus .category-" + k + " .currentStatus").html("ERFOLGREICH");
                            } else {
                                $("#MyBookingsRESAdmin_categoryStatus .category-" + k + " .currentStatus").html("FEHLER");
                            }
                        }
                    }

                    if (MyBookingsRESAdmin.syncCategoryData.currentIdx < MyBookingsRESAdmin.syncCategoryData.syncPackageCount-1) {
                        MyBookingsRESAdmin.syncCategoryData.go();
                    } else {
                        // create pages

                        MyBookingsRESAdmin.createCategoryPages();
                        MyBookingsRESAdmin.syncAreas();
                        MyBookingsRESAdmin.syncLocations();
                    }
        
                },
                error: function() {
                    //any error to be handled
                }
            });
            
        }
    };

    MyBookingsRESAdmin.initAreaAccordionsForAttributeFilters = function() {
        let accordions = document.getElementsByClassName("mybookingsres-area-header mybookingsres-attributes-filter");

        for (let i = 0; i < accordions.length; i++) {
            accordions[i].addEventListener("click", function() {

                let handlerContext = this;

                if (accordions[i].classList.contains('mybookingsres-area-header-active')) {
                    handlerContext.classList.toggle("mybookingsres-area-header-active");
                    let panel = handlerContext.nextElementSibling;
                    if (panel.style.maxHeight) {
                        panel.style.maxHeight = null;
                    } else {
                        panel.style.maxHeight = panel.scrollHeight + "px";
                    }
                    return;
                }

                MyBookingsRESAdmin.setLoader(true);

                $.ajax ({
                    method: 'post',
                    url: ajax.url,
                    dataType: "json",
                    data: {
                        action: 'MyBookingsRES_loadAreaFilterDataForAttributes',
                        areaId: accordions[i].id
                    },
                    success: function (d) {
                        if (d && d.data) {
                            let attributes = d.data;

                            let areaBody = accordions[i].parentElement.getElementsByClassName('mybookingsres-area-body')[0];

                            let filterItems = '';
                            attributes.forEach(function (attribute) {
                                filterItems += ("<div class='mybookingsres-area-filter-item'>"
                                        + "<input id='attribute-"+ accordions[i].id + "-" + attribute.attribut_id +"' type='checkbox'" + (attribute.assignment ? "checked" : "") + ">"
                                        + "<label for='attribute-"+ accordions[i].id + "-" + attribute.attribut_id +"'>" + attribute.desc_de + "</label>" +
                                        "</div>");
                            });
                            areaBody.innerHTML = "<div class='mybookingsres-area-filter-section'>" + filterItems + "</div>"
                                                + "<div class='mybookingsres-d-flex mybookingsres-justify-content-end mybookingsres-align-items-center m-3'>"
                                                +   "<button onclick='MyBookingsRESAdmin.saveFilterSettingsForAttributes("+ accordions[i].id +")' class='mybookingsres-btn mybookingsres-btn-primary'>Speichern</button>"
                                                + "</div>";

                            handlerContext.classList.toggle("mybookingsres-area-header-active");
                            let panel = handlerContext.nextElementSibling;
                            if (panel.style.maxHeight) {
                                panel.style.maxHeight = null;
                            } else {
                                panel.style.maxHeight = panel.scrollHeight + "px";
                            }

                            //MyBookingsRESAdmin.loadAreaFilterAssignments(accordions[i].id, handlerContext);
                        }
                        MyBookingsRESAdmin.setLoader(false);
                    },
                    error: function() {
                        //any error to be handled
                    }
                });
            });
        }
    };

    MyBookingsRESAdmin.initAreaAccordionsForDistanceFilters = function() {
        let accordions = document.getElementsByClassName("mybookingsres-area-header mybookingsres-distances-filter");

        for (let i = 0; i < accordions.length; i++) {
            accordions[i].addEventListener("click", function() {

                let handlerContext = this;

                if (accordions[i].classList.contains('mybookingsres-area-header-active')) {
                    handlerContext.classList.toggle("mybookingsres-area-header-active");
                    let panel = handlerContext.nextElementSibling;
                    if (panel.style.maxHeight) {
                        panel.style.maxHeight = null;
                    } else {
                        panel.style.maxHeight = panel.scrollHeight + "px";
                    }
                    return;
                }

                MyBookingsRESAdmin.setLoader(true);

                $.ajax ({
                    method: 'post',
                    url: ajax.url,
                    dataType: "json",
                    data: {
                        action: 'MyBookingsRES_loadAreaFilterDataForDistances',
                        areaId: accordions[i].id
                    },
                    success: function (d) {
                        if (d && d.data) {
                            let distances = d.data;

                            let areaBody = accordions[i].parentElement.getElementsByClassName('mybookingsres-area-body')[0];

                            let filterItems = '';
                            distances.forEach(function (distance) {
                                filterItems += ("<div class='mybookingsres-area-filter-item'>"
                                    + "<input id='distance-"+ accordions[i].id + "-" + distance.field_id +"' type='checkbox'" + (distance.assignment ? "checked" : "") + ">"
                                    + "<label for='distance-"+ accordions[i].id + "-" + distance.field_id +"'>" + distance.label_de + "</label>" +
                                    "</div>");
                            });
                            areaBody.innerHTML = "<div class='mybookingsres-area-filter-section'>" + filterItems + "</div>"
                                + "<div class='mybookingsres-d-flex mybookingsres-justify-content-end mybookingsres-align-items-center m-3'>"
                                +   "<button onclick='MyBookingsRESAdmin.saveFilterSettingsForDistances("+ accordions[i].id +")' class='mybookingsres-btn mybookingsres-btn-primary'>Speichern</button>"
                                + "</div>";

                            handlerContext.classList.toggle("mybookingsres-area-header-active");
                            let panel = handlerContext.nextElementSibling;
                            if (panel.style.maxHeight) {
                                panel.style.maxHeight = null;
                            } else {
                                panel.style.maxHeight = panel.scrollHeight + "px";
                            }

                            //MyBookingsRESAdmin.loadAreaFilterAssignments(accordions[i].id, handlerContext);
                        }
                        MyBookingsRESAdmin.setLoader(false);
                    },
                    error: function() {
                        //any error to be handled
                    }
                });
            });
        }
    };

    MyBookingsRESAdmin.saveFilterSettingsForAttributes = function(areaId) {
        let attributes = [];

        $("#" + areaId + ".mybookingsres-attributes-filter").parent().find("input:checked").each(function () {
            attributes.push($(this).attr('id'));
        });

        let attributeIds = [];

        attributes.forEach(function (attribute) {
            attributeIds.push(attribute.split('-')[2]);
        });

        MyBookingsRESAdmin.setLoader(true);

        $.ajax ({
            method: 'post',
            url: ajax.url,
            dataType: "json",
            data: {
                action: 'MyBookingsRES_saveAreaFilterDataForAttributes',
                areaId: areaId,
                attributeIds: attributeIds
            },
            success: function () {
                MyBookingsRESAdmin.setLoader(false);
            },
            error: function() {
                //any error to be handled
            }
        });
    };

    MyBookingsRESAdmin.saveFilterSettingsForDistances = function(areaId) {
        let distances = [];

        $("#" + areaId + ".mybookingsres-distances-filter").parent().find("input:checked").each(function () {
            distances.push($(this).attr('id'));
        });

        let distanceIds = [];

        distances.forEach(function (distance) {
            distanceIds.push(distance.split('-')[2]);
        });

        MyBookingsRESAdmin.setLoader(true);

        $.ajax ({
            method: 'post',
            url: ajax.url,
            dataType: "json",
            data: {
                action: 'MyBookingsRES_saveAreaFilterDataForDistances',
                areaId: areaId,
                distanceIds: distanceIds
            },
            success: function () {
                MyBookingsRESAdmin.setLoader(false);
            },
            error: function() {
                //any error to be handled
            }
        });
    };

    MyBookingsRESAdmin.loadAreaFilterAssignments = function(areaId, handlerContext) {

        $.ajax ({
            method: 'post',
            url: ajax.url,
            dataType: "json",
            data: {
                action: 'MyBookingsRES_loadAreaFilterAssignmentsForAttributes',
                areaId: areaId
            },
            success: function (d) {
                if (d && d.data && d.error !== 1) {
                    let assignments = d.data;

                    assignments.forEach(function (assignment) {
                        $("#attribute-" + assignment.area_id + "-" + assignment.attribute_id).prop('checked', true);
                    });
                }

                handlerContext.classList.toggle("mybookingsres-area-header-active");
                let panel = handlerContext.nextElementSibling;
                if (panel.style.maxHeight) {
                    panel.style.maxHeight = null;
                } else {
                    panel.style.maxHeight = panel.scrollHeight + "px";
                }
            },
            error: function() {

            }
        });

    };

    MyBookingsRESAdmin.setLoader = function(isLoading) {
        if (Boolean(isLoading) === true) {
            $("body").addClass("mybookingsres-loading");
        } else {
            $("body").removeClass("mybookingsres-loading");
        }
    };

    $(function() {

        /*
        NOTE: Global loader cannot be used on all ajax calls. 'wp-auth-check' ajax call triggers it as well.

        $(this).on({
            ajaxStart: function() { $("body").addClass("mybookingsres-loading");},
            ajaxStop: function() { $("body").removeClass("mybookingsres-loading");}
        });
         */

        MyBookingsRESAdmin.initAreaAccordionsForAttributeFilters();

        MyBookingsRESAdmin.initAreaAccordionsForDistanceFilters();

        $("#MyBookingsRES_settings_save").click(function(e) {

            MyBookingsRESAdmin.saveSettings();

            e.preventDefault();
        });

        $("#MyBookingsRESAdmin_initSync").click(function(e) {

            MyBookingsRESAdmin.deleteUnusedCategories();

            e.preventDefault();
        });

        $("#MyBookingsRESAdmin_createPagesButton").click(function(e) {

            MyBookingsRESAdmin.createCategoryPages();

            e.preventDefault();
        });

        $("#MyBookingsRES_removeCategoryPages").click(function(e) {

            MyBookingsRESAdmin.removeCategoryPages();

            e.preventDefault();
        });

        $("#MyBookingsRES_createResultPages").click(function(e) {

            MyBookingsRESAdmin.createResultPages();

            e.preventDefault();
        });

        $("#MyBookingsRES_createPaymentReturnPages").click(function(e) {

            MyBookingsRESAdmin.createPaymentReturnPages();

            e.preventDefault();
        });

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