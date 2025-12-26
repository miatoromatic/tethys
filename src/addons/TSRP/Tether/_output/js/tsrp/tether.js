(function ()
{
    "use strict";

    function escapeHtml(value)
    {
        return String(value)
            .replace(/&/g, "&amp;")
            .replace(/</g, "&lt;")
            .replace(/>/g, "&gt;")
            .replace(/"/g, "&quot;")
            .replace(/'/g, "&#039;");
    }

    function buildLevelList(label, levels, selected)
    {
        if (!levels || typeof levels !== "object")
        {
            return "<div class=\"tsrp-tether__levels-group\">" +
                "<div class=\"tsrp-tether__levels-title\">" + escapeHtml(label) + "</div>" +
                "<div class=\"blockMessage\">No " + escapeHtml(label.toLowerCase()) + " tiers listed.</div>" +
                "</div>";
        }

        var items = Object.keys(levels).map(function (level)
        {
            var unlocked = selected.indexOf(String(level)) !== -1;
            return "<li class=\"tsrp-tether__level" + (unlocked ? " is-unlocked" : "") + "\">" +
                "<strong>" + escapeHtml(label) + " " + escapeHtml(level) + ":</strong> " +
                escapeHtml(levels[level]) +
                "</li>";
        }).join("");

        return "<div class=\"tsrp-tether__levels-group\">" +
            "<div class=\"tsrp-tether__levels-title\">" + escapeHtml(label) + "</div>" +
            "<ul class=\"tsrp-tether__list\">" + items + "</ul>" +
            "</div>";
    }

    function renderPopupHtml(template, data, selectedPositive, selectedNegative)
    {
        var tags = Array.isArray(data.tags)
            ? data.tags.map(function (tag)
            {
                return "<span class=\"tsrp-tether__tag\">" + escapeHtml(tag) + "</span>";
            }).join(" ")
            : "";

        var positiveLevels = buildLevelList("Positive", data.positive, selectedPositive);
        var negativeLevels = buildLevelList("Negative", data.negative, selectedNegative);

        return template
            .replace(/\{\{title\}\}/g, escapeHtml(data.title || "Tether"))
            .replace(/\{\{category\}\}/g, escapeHtml(data.category || ""))
            .replace(/\{\{image\}\}/g, escapeHtml(data.image || ""))
            .replace(/\{\{tags\}\}/g, tags)
            .replace(/\{\{description\}\}/g, escapeHtml(data.description || ""))
            .replace(/\{\{wiki\}\}/g, escapeHtml(data.wiki || ""))
            .replace(/\{\{positiveLevels\}\}/g, positiveLevels)
            .replace(/\{\{negativeLevels\}\}/g, negativeLevels);
    }

    XF.TsrpTether = XF.Element.newHandler({
        init: function ()
        {
            var templateEl = document.querySelector(".js-tsrp-tether-popup-template");
            this.templateHtml = templateEl ? templateEl.innerHTML : "";

            this.$target.on("click", this.onClick.bind(this));
        },

        onClick: function (event)
        {
            event.preventDefault();
            var element = this.$target[0];
            var jsonUrl = element.getAttribute("data-tether-json");
            var title = element.getAttribute("data-tether-title") || "Tether";
            var selectedPositive = (element.getAttribute("data-selected-positive") || "").split(",").filter(Boolean);
            var selectedNegative = (element.getAttribute("data-selected-negative") || "").split(",").filter(Boolean);

            if (!this.templateHtml)
            {
                XF.alert("Missing tether popup template.", title);
                return;
            }

            if (!jsonUrl)
            {
                XF.alert("Missing tether data URL.", title);
                return;
            }

            fetch(jsonUrl, { credentials: "same-origin" })
                .then(function (response)
                {
                    if (!response.ok)
                    {
                        throw new Error("Unable to load tether data.");
                    }
                    return response.json();
                })
                .then(function (data)
                {
                    var html = renderPopupHtml(this.templateHtml, data, selectedPositive, selectedNegative);
                    XF.alert(html, title);
                }.bind(this))
                .catch(function ()
                {
                    XF.alert("Unable to load tether data.", title);
                });
        }
    });

    XF.Element.register("tsrp-tether", "XF.TsrpTether");
})();
