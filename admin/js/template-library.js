"use strict";
!function (e, t, i) {
    var r = {
        Views: {},
        Models: {},
        Collections: {},
        Behaviors: {},
        Layout: null,
        Manager: null
    };
    r.Models.Template = Backbone.Model.extend({
        defaults: {
            template_id: 0,
            title: "",
            type: "",
            thumbnail: "",
            url: "",
            tags: [],
        }
    }), r.Collections.Template = Backbone.Collection.extend({
        model: r.Models.Template
    }), r.Behaviors.InsertTemplate = Marionette.Behavior.extend({
        ui: {
            insertButton: ".workreapTemplateLibrary__insert-button"
        },
        events: {
            "click @ui.insertButton": "onInsertButtonClick"
        },
        onInsertButtonClick: function () {
            i.library.insertTemplate({
                model: this.view.model
            })
        }
    }), r.Views.EmptyTemplateCollection = Marionette.ItemView.extend({
        id: "elementor-template-library-templates-empty",
        template: "#tmpl-workreapTemplateLibrary__empty",
        ui: {
            title: ".elementor-template-library-blank-title",
            message: ".elementor-template-library-blank-message"
        },
        modesStrings: {
            empty: {
                title: "Templates Not Found",
                message: "Something Went Wrong Please Check"
            },
            noResults: {
                title: "No Result Found",
                message: "Sorry, but nothing matched your selection"
            }
        },
        getCurrentMode: function () {
            return i.library.getFilter("text") ? "noResults" : "empty"
        },
        onRender: function () {
            var e = this.modesStrings[this.getCurrentMode()];
            this.ui.title.html(e.title), this.ui.message.html(e.message)
        }
    }), r.Views.Loading = Marionette.ItemView.extend({
        template: "#tmpl-workreapTemplateLibrary__loading",
        id: "workreapTemplateLibrary__loading"
    }), r.Views.Logo = Marionette.ItemView.extend({
        template: "#tmpl-workreapTemplateLibrary__header-logo",
        className: "workreapTemplateLibrary__header-logo",
        templateHelpers: function () {
            return {
                title: this.getOption("title")
            }
        }
    }), r.Views.BackButton = Marionette.ItemView.extend({
        template: "#tmpl-workreapTemplateLibrary__header-back",
        id: "elementor-template-library-header-preview-back",
        className: "workreapTemplateLibrary__header-back",
        events: function () {
            return {
                click: "onClick"
            }
        },
        onClick: function () {
            i.library.showTemplatesView()
        }
    }), r.Views.Menu = Marionette.ItemView.extend({
        template: "#tmpl-workreapTemplateLibrary__header-menu",
        id: "elementor-template-library-header-menu",
        className: "workreapTemplateLibrary__header-menu",
        templateHelpers: function () {
            return i.library.getTabs()
        },
        ui: {
            menuItem: ".elementor-template-library-menu-item"
        },
        events: {
            "click @ui.menuItem": "onMenuItemClick"
        },
        onMenuItemClick: function (e) {
            i.library.setFilter("tags", ""), i.library.setFilter("text", ""), i.library.setFilter("type", e.currentTarget.dataset.tab, !0), i.library.showTemplatesView()
        }
    }), r.Views.ResponsiveMenu = Marionette.ItemView.extend({
        template: "#tmpl-workreapTemplateLibrary__header-menu-responsive",
        id: "elementor-template-library-header-menu-responsive",
        className: "workreapTemplateLibrary__header-menu-responsive",
        ui: {
            items: "> .elementor-component-tab"
        },
        events: {
            "click @ui.items": "onTabItemClick"
        },
        onTabItemClick: function (t) {
            var r = e(t.currentTarget),
                n = r.data("tab");
            i.library.channels.tabs.trigger("change:device", n, r)
        }
    }), r.Views.Actions = Marionette.ItemView.extend({
        template: "#tmpl-workreapTemplateLibrary__header-actions",
        id: "elementor-template-library-header-actions",
        ui: {
            sync: "#workreapTemplateLibrary__header-sync i"
        },
        events: {
            "click @ui.sync": "onSyncClick"
        },
        onSyncClick: function () {
            var e = this;
            e.ui.sync.addClass("eicon-animation-spin"), i.library.requestLibraryData({
                onUpdate: function () {
                    e.ui.sync.length && e.ui.sync.removeClass("eicon-animation-spin"), i.library.updateBlocksView()
                },
                forceUpdate: !0,
                forceSync: !0
            })
        }
    }), r.Views.InsertWrapper = Marionette.ItemView.extend({
        template: "#tmpl-workreapTemplateLibrary__header-insert",
        id: "elementor-template-library-header-preview",
        behaviors: {
            insertTemplate: {
                behaviorClass: r.Behaviors.InsertTemplate
            }
        }
    }), r.Views.Preview = Marionette.ItemView.extend({
        template: "#tmpl-workreapTemplateLibrary__preview",
        className: "workreapTemplateLibrary__preview",
        ui: function () {
            return {
                iframe: "> iframe"
            }
        },
        onRender: function () {
            this.ui.iframe.attr("src", this.getOption("url")).hide();
            var e = this,
                t = new r.Views.Loading().render();
            this.$el.append(t.el), this.ui.iframe.on("load", function () {
                e.$el.find("#workreapTemplateLibrary__loading").remove(), e.ui.iframe.show()
            })
        }
    }), r.Views.TemplateCollection = Marionette.CompositeView.extend({
        template: "#tmpl-workreapTemplateLibrary__templates",
        id: "workreapTemplateLibrary__templates",
        className: function () {
            return "workreapTemplateLibrary__templates workreapTemplateLibrary__templates--" + i.library.getFilter("type")
        },
        childViewContainer: "#workreapTemplateLibrary__templates-list",
        emptyView: function () {
            return new r.Views.EmptyTemplateCollection
        },
        ui: {
            templatesWindow: ".workreapTemplateLibrary__templates-window",
            textFilter: "#workreapTemplateLibrary__search",
            tagsFilter: "#workreapTemplateLibrary__filter-tags",
            filterBar: "#workreapTemplateLibrary__toolbar-filter",
            counter: "#workreapTemplateLibrary__toolbar-counter"
        },
        events: {
            "input @ui.textFilter": "onTextFilterInput",
            "click @ui.tagsFilter li": "onTagsFilterClick"
        },
        getChildView: function (e) {
            return r.Views.Template
        },
        initialize: function () {
            this.listenTo

            (i.library.channels.templates, "filter:change", this._renderChildren)
        },
        filter: function (e) {
            var t = i.library.getFilterTerms(),
                r = !0;
            return _.each(t, function (t, n) {
                var a = i.library.getFilter(n);
                if (a && t.callback) {
                    var o = t.callback.call(e, a);
                    return o || (r = !1), o
                }
            }), r
        },
        setMasonrySkin: function () {
            if ("section" === i.library.getFilter("type")) {
                var e = new elementorModules.utils.Masonry({
                    container: this.$childViewContainer,
                    items: this.$childViewContainer.children()
                });
                this.$childViewContainer.imagesLoaded(e.run.bind(e))
            }
        },
        onRenderCollection: function () {
            this.setMasonrySkin(), this.updatePerfectScrollbar(), this.setTemplatesFoundText()
        },
        setTemplatesFoundText: function () {
            var e = i.library.getFilter("type"),
                t = this.children.length,
                r = "<b>" + t + "</b>";
            r += "section" === e ? " block" : " " + e, t > 1 && (r += "s"), r += " found", this.ui.counter.html(r)
        },
        onTextFilterInput: function () {
            var e = this;
            _.defer(function () {
                i.library.setFilter("text", e.ui.textFilter.val())
            })
        },
        onTagsFilterClick: function (t) {
            var r = e(t.currentTarget),
                n = r.data("tag");
            i.library.setFilter("tags", n), r.addClass("active").siblings().removeClass("active"), n = n ? i.library.getTags()[n] : "Filter", this.ui.filterBar.find(".workreapTemplateLibrary__filter-btn").html(n + ' <i class="eicon-caret-down"></i>')
        },
        updatePerfectScrollbar: function () {
            this.perfectScrollbar || (this.perfectScrollbar = new PerfectScrollbar(this.ui.templatesWindow[0], {
                suppressScrollX: !0
            })), this.perfectScrollbar.isRtl = !1, this.perfectScrollbar.update()
        },
        setTagsFilterHover: function () {
            var e = this;
            e.ui.filterBar.hoverIntent(function () {
                e.ui.tagsFilter.css("display", "block"), e.ui.filterBar.find(".workreapTemplateLibrary__filter-btn i").addClass("eicon-caret-down").removeClass("eicon-caret-right")
            }, function () {
                e.ui.tagsFilter.css("display", "none"), e.ui.filterBar.find(".workreapTemplateLibrary__filter-btn i").addClass("eicon-caret-right").removeClass("eicon-caret-down")
            }, {
                sensitivity: 50,
                interval: 150,
                timeout: 100
            })
        },
        onRender: function () {
            this.setTagsFilterHover(), this.updatePerfectScrollbar()
        }
    }), r.Views.Template = Marionette.ItemView.extend({
        template: "#tmpl-workreapTemplateLibrary__template",
        className: "workreapTemplateLibrary__template",
        ui: {
            previewButton: ".workreapTemplateLibrary__preview-button, .workreapTemplateLibrary__template-preview"
        },
        events: {
            "click @ui.previewButton": "onPreviewButtonClick"
        },
        behaviors: {
            insertTemplate: {
                behaviorClass: r.Behaviors.InsertTemplate
            }
        },
        onPreviewButtonClick: function () {
            i.library.showPreviewView(this.model)
        }
    }), r.Modal = elementorModules.common.views.modal.Layout.extend({
        getModalOptions: function () {
            return {
                id: "workreapTemplateLibrary__modal",
                hide: {
                    onOutsideClick: !1,
                    onEscKeyPress: !0,
                    onBackgroundClick: !1
                }
            }
        },
        getTemplateActionButton: function (e) {
            var t = "#tmpl-workreapTemplateLibrary__" + 'insert-button',
                i = Marionette.TemplateCache.get(t);
            return Marionette.Renderer.render(i)
        },
        showLogo: function (e) {
            this.getHeaderView().logoArea.show(new r.Views.Logo(e))
        },
        showDefaultHeader: function () {
            this.showLogo({
                title: "WORKREAP LIBRARY"
            });
            var e = this.getHeaderView();
            e.tools.show(new r.Views.Actions), e.menuArea.show(new r.Views.Menu)
        },
        showPreviewView: function (e) {
            var t = this.getHeaderView();
            t.menuArea.show(new r.Views.ResponsiveMenu), t.logoArea.show(new r.Views.BackButton), t.tools.show(new r.Views.InsertWrapper({
                model: e
            })), this.modalContent.show(new r.Views.Preview({
                url: e.get("url")
            }))
        },
        showTemplatesView: function (e) {
            this.showDefaultHeader(), this.modalContent.show(new r.Views.TemplateCollection({
                collection: e
            }))
        }
    }), r.Manager = function () {
        function i() {
            var i = e(this).closest(".elementor-top-section"),
                r = i.data("id"),
                n = t.documents.getCurrent().container.children,
                a = i.prev(".elementor-add-section");
            n && _.each(n, function (e, t) {
                r === e.id && (p.atIndex = t)
            }), a.find(".elementor-add-workreap-button").length || a.find(d).before(m)
        }

        function n(t, i) {
            i.addClass("elementor-active").siblings().removeClass("elementor-active");
            var r = u[t] || u.desktop;
            e(".workreapTemplateLibrary__preview").css("width", r)
        }

        var a, o, l, s, c, p = this,
            d = ".elementor-add-new-section .elementor-add-section-drag-title",
            m = `<div class="elementor-add-section-area-button elementor-add-workreap-button"><img src="${WorkreapLibraryArgs.wr_icon}" alt="workreap-icon"></div>`,
            u = {
                desktop: "100%",
                tab: "680px",
                mobile: "360px"
            };
        this.atIndex = -1, this.channels = {
            tabs: Backbone.Radio.channel("tabs"),
            templates: Backbone.Radio.channel("templates")
        }, this.updateBlocksView = function () {
            p.setFilter("tags", "", !0), p.setFilter("text", "", !0), p.getModal().showTemplatesView(s)
        }, this.setFilter = function (e, t, i) {
            p.channels.templates.reply("filter:" + e, t), i || p.channels.templates.trigger("filter:change")
        }, this.getFilter = function (e) {
            return p.channels.templates.request("filter:" + e)
        }, this.getFilterTerms = function () {
            return {
                tags: {
                    callback: function (e) {
                        return _.any(this.get("tags"), function (t) {
                            return t.indexOf(e) >= 0
                        })
                    }
                },
                text: {
                        callback: function (e) {
                            return e = e.toLowerCase(), this.get("title").toLowerCase().indexOf(e) >= 0 || _.any(this.get("tags"), function (t) {
                                return t.indexOf(e) >= 0
                            })
                        }
                    },
                type: {
                    callback: function (e) {
                        return this.get("type") === e
                    }
                }
            }
        }, this.showModal = function () {
            p.getModal().showModal(), p.showTemplatesView()
        }, this.closeModal = function () {
            this.getModal().hideModal()
        }, this.getModal = function () {
            return a || (a = new r.Modal), a
        }, this.init = function () {
            p.setFilter("type", "section", !0), t.on("preview:loaded", (function () {
                var e = window.elementor.$previewContents,
                    t = setInterval(function () {
                        var r, n;
                        (n = (r = e).find(d)).length && !r.find(".elementor-add-workreap-button").length && n.before(m), r.on("click.onAddElement", ".elementor-editor-section-settings .elementor-editor-element-add", i), e.find(".elementor-add-new-section").length > 0 && clearInterval(t)
                    }, 100);
                e.on("click.onAddTemplateButton", ".elementor-add-workreap-button", p.showModal.bind(p)), this.channels.tabs.on("change:device", n)
            }).bind(this))
        }, this.getTabs = function () {
            var e = this.getFilter("type"),
                t = {
                    section: {
                        title: "Blocks"
                    },
                    page: {
                        title: "Pages"
                    }
                };
            return _.each(t, function (i, r) {
                e === r && (t[e].active = !0)
            }), {
                tabs: t
            }
        }, this.getTags = function () {
            return o
        }, this.getTypeTags = function () {
            return l[p.getFilter("type")]
        }, this.showTemplatesView = function () {
            p.setFilter("tags", "", !0), p.setFilter("text", "", !0), s ? p.getModal().showTemplatesView(s) : p.loadTemplates(function () {
                p.getModal().showTemplatesView(s)
            })
        }, this.showPreviewView = function (e) {
            p.getModal().showPreviewView(e)
        }, this.loadTemplates = function (e) {
            p.requestLibraryData({
                onBeforeUpdate: p.getModal().showLoadingView.bind(p.getModal()),
                onUpdate: function () {
                    p.getModal().hideLoadingView(), e && e()
                }
            })
        }, this.requestLibraryData = function (e) {
            if (!s || e.forceUpdate) {
                e.onBeforeUpdate && e.onBeforeUpdate();
                var t = {
                    data: {},
                    success: function (t) {
                        s = new r.Collections.Template(t.templates), t.tags && (o = t.tags), t.type_tags && (l = t.type_tags), e.onUpdate && e.onUpdate()
                    }
                };
                e.forceSync && (t.data.sync = !0), elementorCommon.ajax.addRequest("get_workreap_templates", t)
            } else e.onUpdate && e.onUpdate()
        }, this.requestTemplateData = function (e, t) {
            var i = {
                unique_id: e,
                data: {
                    edit_mode: !0,
                    display: !0,
                    template_id: e
                }
            };
            t && jQuery.extend(!0, i, t), elementorCommon.ajax.addRequest("import_workreap_template", i)
        }, this.insertTemplate = function (e) {
            var t = e.model,
                i = this;
            i.getModal().showLoadingView(), i.requestTemplateData(t.get("template_id"), {
                success: function (e) {
                    i.getModal().hideLoadingView(), i.getModal().hideModal();
                    var r = {};
                    -1 !== i.atIndex && (r.at = i.atIndex), $e.run("document/elements/import", {
                        model: t,
                        data: e,
                        options: r
                    }), i.atIndex = -1
                },
                error: function (e) {
                    i.showErrorDialog(e)
                },
                complete: function (e) {
                    i.getModal().hideLoadingView(), window.elementor.$previewContents.find(".elementor-add-section .elementor-add-section-close").click()
                }
            })
        }, this.showErrorDialog = function (e) {
            if ("object" === _typeof(e)) {
                var t = "";
                _.each(e, function (e) {
                    t += "<div>" + e.message + ".</div>"
                }), e = t
            } else e ? e += "." : e = "<i>&#60;The error message is empty&#62;</i>";
            p.getErrorDialog().setMessage('The following error(s) occurred while processing the request:<div id="elementor-template-library-error-info">' + e + "</div>").show()
        }, this.getErrorDialog = function () {
            return c || (c = elementorCommon.dialogsManager.createWidget("alert", {
                id: "elementor-template-library-error-dialog",
                headerMessage: "An error occurred"
            })), c
        }
    }, i.library = new r.Manager, i.library.init(), window.workreap = i;

    function _typeof(e) {
        return "function" == typeof Symbol && "symbol" == typeof Symbol.iterator ? function (e) {
            return typeof e
        } : function (e) {
            return e && "function" == typeof Symbol && e.constructor === Symbol && e !== Symbol.prototype ? "symbol" : typeof e
        }
    }

}(jQuery, window.elementor, window.workreap || {});