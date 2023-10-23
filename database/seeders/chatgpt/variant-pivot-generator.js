const fs = require('fs');

try {
    const options = JSON.parse(fs.readFileSync('../test-data/test-store-1/product-attribute-options.json', 'utf8'));
    const variants = JSON.parse(fs.readFileSync('../test-data/test-store-1/product-variants.json', 'utf8'));
    let results = []
    variants.forEach(item => {
        item.attribute_options.forEach(option => {
            results.push({
                product_variant_id: item.slug,
                product_attribute_id: options.find(el => {return el.slug === option})?.product_attribute_id,
                product_attribute_option_id: option
            })
        })
    })
    console.log(JSON.stringify(results));
} catch (err) {
    console.error(err);
}
