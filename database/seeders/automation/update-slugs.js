const fs = require('fs');

try {
    const products = JSON.parse(fs.readFileSync('../test-data/test-store-1/products.json', 'utf8'));
    let results = []
    products.forEach(item => {
        item.slug = item.name.toLowerCase().replaceAll(" ", "-").replaceAll("'", "-")
        results.push(item)
    })
    console.log(JSON.stringify(results));
} catch (err) {
    console.error(err);
}
