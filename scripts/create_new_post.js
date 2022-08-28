module.exports = async (params) => {
    QuickAdd = params;

    const title = await QuickAdd.quickAddApi.inputPrompt("Blog - Title");
    var slug = await QuickAdd.quickAddApi.inputPrompt("Blog - Slug");
    const category = await QuickAdd.quickAddApi.checkboxPrompt(["计算机", "青梅煮酒", "行见"], ["计算机"]);

    if (typeof slug === 'undefined') {
        slug = title;
    }

    QuickAdd.variables["articleTitle"] = title;
    QuickAdd.variables["articleSlug"] = slug;
    QuickAdd.variables["articleFilename"] = slug.toLowerCase().replace(/[^A-Za-z0-9\s]/g, '').replace(/\s+/g, '-');
    QuickAdd.variables["articleCategory"] = category;
    QuickAdd.variables["articleTimestamp"] = QuickAdd.quickAddApi.date.now('YYYY-MM-DDTHH:mm:ssZ');

    console.log(QuickAdd.variables);
};
