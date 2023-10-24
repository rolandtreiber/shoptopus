const productList = [
    { fileName: "ladies-boots-1.jpg", slug: "classic-ankle-boots"},
    { fileName: "ladies-boots-1-dark-blue.jpg", slug: "ladies-boots-1-dark-blue-", variants: ['8', '85', '9', '95', '10']},
    { fileName: "ladies-boots-1-green.jpg", slug: "ladies-boots-1-dark-green-", variants: ['8', '85', '9', '95', '10']},
    { fileName: "ladies-boots-2.jpg", slug: "lace-up-combat-boots"},
    { fileName: "ladies-boots-3.jpg", slug: "fashion-knee-high-boots"},
    { fileName: "ladies-hats-1.jpg", slug: "wide-brim-straw-hat"},
    { fileName: "ladies-hats-2.jpg", slug: "classic-fedora-hat"},
    { fileName: "ladies-hats-3.jpg", slug: "classic-fedora-hat"},
    { fileName: "ladies-jewellery-1.jpg", slug: "crystal-drop-earrings"},
    { fileName: "ladies-jewellery-2.jpg", slug: "pearl-stud-earrings"},
    { fileName: "ladies-jewellery-3.jpg", slug: "statement-tassel-earrings"},
    { fileName: "ladies-jumpers-1.jpg", slug: "classic-wool-jumper"},
    { fileName: "ladies-jumpers-2.jpg", slug: "casual-v-neck-jumper"},
    { fileName: "ladies-jumpers-3.jpg", slug: "sporty-zip-up-jumper-1"},
    { fileName: "ladies-jumpers-3-bright-blue.jpg", slug: "ladies-jumpers-3-bright-blue-", variants: ['xs', 's', 'm', 'l', 'xl']},
    { fileName: "ladies-jumpers-3-green.jpg", slug: "ladies-jumpers-3-bright-green-", variants: ['xs', 's', 'm', 'l', 'xl']},
    { fileName: "ladies-platform-shoes-1.jpg", slug: "platform-slip-on-shoes"},
    { fileName: "ladies-platform-shoes-1-2.jpg", slug: "platform-slip-on-shoes"},
    { fileName: "ladies-platform-shoes-1-3.jpg", slug: "platform-slip-on-shoes"},
    { fileName: "ladies-platform-shoes-2.jpg", slug: "chunky-heel-platform-sandals"},
    { fileName: "ladies-platform-shoes-3.jpg", slug: "lace-up-platform-boots"},
    { fileName: "ladies-polo-shirts-1.jpg", slug: "classic-polo-shirt"},
    { fileName: "ladies-polo-shirts-1-dark-blue.jpg", slug: "ladies-polo-shirts-1-dark-blue-", variants: ['xs', 's', 'm', 'l', 'xl']},
    { fileName: "ladies-polo-shirts-1-green.jpg", slug: "ladies-polo-shirts-1-bright-green-", variants: ['xs', 's', 'm', 'l', 'xl']},
    { fileName: "ladies-polo-shirts-1-light-blue.jpg", slug: "ladies-polo-shirts-1-bright-blue-", variants: ['xs', 's', 'm', 'l', 'xl']},
    { fileName: "ladies-polo-shirts-1-pink.jpg", slug: "ladies-polo-shirts-1-pink-", variants: ['xs', 's', 'm', 'l', 'xl']},
    { fileName: "ladies-polo-shirts-1-red.jpg", slug: "ladies-polo-shirts-1-bright-red-", variants: ['xs', 's', 'm', 'l', 'xl']},
    { fileName: "ladies-polo-shirts-2.jpg", slug: "sporty-performance-polo"},
    { fileName: "ladies-polo-shirts-3.jpg", slug: "casual-short-sleeve-polo"},
    { fileName: "ladies-polo-shirts-3-bright-blue.jpg", slug: "ladies-polo-shirts-3-bright-blue-", variants: ['xs', 's', 'm', 'l', 'xl']},
    { fileName: "ladies-polo-shirts-3-green.jpg", slug: "ladies-polo-shirts-3-bright-green-", variants: ['xs', 's', 'm', 'l', 'xl']},
    { fileName: "ladies-polo-shirts-3-pink.jpg", slug: "ladies-polo-shirts-3-pink-", variants: ['xs', 's', 'm', 'l', 'xl']},
    { fileName: "ladies-polo-shirts-3_1.jpg", slug: "casual-short-sleeve-polo"},
    { fileName: "size-chart.jpg", slug: "classic-polo-shirt"},
    { fileName: "size-chart.jpg", slug: "sporty-performance-polo"},
    { fileName: "size-chart.jpg", slug: "casual-short-sleeve-polo"},
    { fileName: "ladies-sandals-1.jpg", slug: "casual-slide-sandals"},
    { fileName: "ladies-sandals-2.jpg", slug: "strappy-beach-sandals"},
    { fileName: "ladies-sandals-3.jpg", slug: "fashion-wedge-sandals"},
    { fileName: "ladies-skirts-1.jpg", slug: "elegant-a-line-skirt"},
    { fileName: "ladies-skirts-1-bright-green.jpg", slug: "ladies-skirts-1-bright-green-", variants: ['xs', 's', 'm', 'l', 'xl']},
    { fileName: "ladies-skirts-1-dark-blue.jpg", slug: "ladies-skirts-1-dark-blue-", variants: ['xs', 's', 'm', 'l', 'xl']},
    { fileName: "ladies-skirts-2.jpg", slug: "casual-denim-skirt"},
    { fileName: "ladies-skirts-3.jpg", slug: "floral-midi-skirt"},
    { fileName: "ladies-sneakers-1.jpg", slug: "casual-slip-on-sneakers"},
    { fileName: "ladies-sneakers-2.jpg", slug: "sporty-running-sneakers"},
    { fileName: "ladies-sneakers-2-green.jpg", slug: "ladies-sneakers-2-bright-green-", variants: ['8', '85', '9', '95', '10']},
    { fileName: "ladies-sneakers-2-red.jpg", slug: "ladies-sneakers-2-bright-red-", variants: ['8', '85', '9', '95', '10']},
    { fileName: "ladies-sneakers-3.jpg", slug: "fashion-high-top-sneakers"},
    { fileName: "ladies-socks-1.jpg", slug: "classic-cotton-crew-socks-1"},
    { fileName: "ladies-socks-1_1.jpg", slug: "classic-cotton-crew-socks-1"},
    { fileName: "ladies-socks-2.jpg", slug: "sporty-ankle-socks-1"},
    { fileName: "ladies-socks-3.jpg", slug: "casual-low-cut-socks-1"},
    { fileName: "ladies-sunglasses-1.jpg", slug: "chic-cat-eye-sunglasses"},
    { fileName: "ladies-sunglasses-2.jpg", slug: "vintage-round-sunglasses"},
    { fileName: "ladies-sunglasses-3.jpg", slug: "sporty-polarized-sunglasses"},
    { fileName: "ladies-trousers-1.jpg", slug: "classic-dress-trousers-1"},
    { fileName: "ladies-trousers-1_1.jpg", slug: "classic-dress-trousers-1"},
    { fileName: "ladies-trousers-2.jpg", slug: "casual-chino-trousers-1"},
    { fileName: "ladies-trousers-2-bright-blue.jpg", slug: "casual-chino-trousers-brightblue-", variants: ['xs', 's', 'm', 'l', 'xl']},
    { fileName: "ladies-trousers-2-green.jpg", slug: "casual-chino-trousers-brightgreen-", variants: ['xs', 's', 'm', 'l', 'xl']},
    { fileName: "ladies-trousers-3.jpg", slug: "sporty-track-pants-1"},
    { fileName: "ladies-trousers-3_1.jpg", slug: "sporty-track-pants-1"},
    { fileName: "mens-boots-1.jpg", slug: "classic-leather-boots"},
    { fileName: "mens-boots-2.jpg", slug: "stylish-chelsea-boots"},
    { fileName: "mens-boots-3.jpg", slug: "outdoor-hiking-boots"},
    { fileName: "mens-boots-3-black-1.jpg", slug: "outdoor-hiking-boots-black-", variants: ['8', '85', '9', '95', '10']},
    { fileName: "mens-boots-3-black-2.jpg", slug: "outdoor-hiking-boots-black-", variants: ['8', '85', '9', '95', '10']},
    { fileName: "mens-boots-3-black-3.jpg", slug: "outdoor-hiking-boots-black-", variants: ['8', '85', '9', '95', '10']},
    { fileName: "mens-boots-3-blue-1.jpg", slug: "outdoor-hiking-boots-midnightblue-", variants: ['8', '85', '9', '95', '10']},
    { fileName: "mens-boots-3-blue-2.jpg", slug: "outdoor-hiking-boots-midnightblue-", variants: ['8', '85', '9', '95', '10']},
    { fileName: "mens-jewellery-1.jpg", slug: "leather-cuff-bracelet"},
    { fileName: "mens-jewellery-2.jpg", slug: "stainless-steel-link-bracelet"},
    { fileName: "mens-jewellery-3.jpg", slug: "beaded-charm-bracelet"},
    { fileName: "mens-jumpers-1.jpg", slug: "classic-elegant-wool-jumper"},
    { fileName: "mens-jumpers-2.jpg", slug: "casual-v-neck-modern-jumper"},
    { fileName: "mens-jumpers-3.jpg", slug: "sporty-zip-up-jumper"},
    { fileName: "mens-loafers-1.jpg", slug: "classic-leather-loafers"},
    { fileName: "mens-loafers-1-gray.jpg", slug: "classic-leather-loafers-darkgray-", variants: ['8', '85', '9', '95', '10']},
    { fileName: "mens-loafers-1-green.jpg", slug: "classic-leather-loafers-darkgreen-", variants: ['8', '85', '9', '95', '10']},
    { fileName: "mens-loafers-2.jpg", slug: "suede-tassel-loafers"},
    { fileName: "mens-loafers-3.jpg", slug: "fashion-slip-on-loafers"},
    { fileName: "mens-sandals-1.jpg", slug: "casual-slide-sandals-1"},
    { fileName: "mens-sandals-2.jpg", slug: "sporty-outdoor-sandals"},
    { fileName: "mens-sandals-2-2.jpg", slug: "sporty-outdoor-sandals"},
    { fileName: "mens-sandals-3.jpg", slug: "fashion-flip-flops"},
    { fileName: "mens-shirt-1.jpg", slug: "classic-dress-shirt"},
    { fileName: "mens-shirt-2.jpg", slug: "business-casual-shirt"},
    { fileName: "mens-shirt-2-midnightblue.jpg", slug: "business-casual-shirt-midnightblue-", variants: ['xs', 's', 'm', 'l', 'xl']},
    { fileName: "mens-shirt-2-bright-green.jpg", slug: "business-casual-shirt-darkgreen-", variants: ['xs', 's', 'm', 'l', 'xl']},
    { fileName: "mens-shirt-2-bright-red.jpg", slug: "business-casual-shirt-darkred-", variants: ['xs', 's', 'm', 'l', 'xl']},
    { fileName: "mens-shirt-3.jpg", slug: "casual-checkered-shirt"},
    { fileName: "mens-sneakers-1.jpg", slug: "sporty-mens-sneakers"},
    { fileName: "mens-sneakers-1 2.jpg", slug: "sporty-mens-sneakers"},
    { fileName: "mens-sneakers-2.jpg", slug: "classic-canvas-sneakers"},
    { fileName: "mens-sneakers-2 2.jpg", slug: "classic-canvas-sneakers"},
    { fileName: "mens-sneakers-3.jpg", slug: "modern-high-top-sneakers"},
    { fileName: "mens-sneakers-3 2.jpg", slug: "modern-high-top-sneakers"},
    { fileName: "mens-sneakers-3-2-red.jpg", slug: "modern-high-top-sneakers-brightred-", variants: ['8', '85', '9', '95', '10']},
    { fileName: "mens-sneakers-3-2-green.jpg", slug: "modern-high-top-sneakers-brightgreen-", variants: ['8', '85', '9', '95', '10']},
    { fileName: "mens-socks-1.jpg", slug: "classic-cotton-crew-socks"},
    { fileName: "mens-socks-2.jpg", slug: "sporty-ankle-socks"},
    { fileName: "mens-socks-3.jpg", slug: "casual-low-cut-socks"},
    { fileName: "mens-sunglasses-1.jpg", slug: "aviator-sunglasses"},
    { fileName: "mens-sunglasses-2.jpg", slug: "sporty-wraparound-sunglasses"},
    { fileName: "mens-sunglasses-3.jpg", slug: "modern-square-sunglasses"},
    { fileName: "mens-t-shirts-1.jpg", slug: "classic-cotton-t-shirt"},
    { fileName: "mens-t-shirts-2.jpg", slug: "sporty-performance-t-shirt"},
    { fileName: "mens-t-shirts-3.jpg", slug: "casual-crew-neck-t-shirt"},
    { fileName: "mens-t-shirts-3-white.jpg", slug: "casual-crew-neck-t-shirt-white-", variants: ['xs', 's', 'm', 'l', 'xl']},
    { fileName: "mens-t-shirts-3-blue.jpg", slug: "casual-crew-neck-t-shirt-brightblue-", variants: ['xs', 's', 'm', 'l', 'xl']},
    { fileName: "mens-t-shirts-3-green.jpg", slug: "casual-crew-neck-t-shirt-brightgreen-", variants: ['xs', 's', 'm', 'l', 'xl']},
    { fileName: "mens-t-shirts-3-orange.jpg", slug: "casual-crew-neck-t-shirt-brightorange-", variants: ['xs', 's', 'm', 'l', 'xl']},
    { fileName: "mens-trousers-1.jpg", slug: "classic-dress-trousers"},
    { fileName: "mens-trousers-2.jpg", slug: "casual-chino-trousers"},
    { fileName: "mens-trousers-3.jpg", slug: "sporty-track-pants"},
    { fileName: "size-chart-shoes.jpg", slug: "sporty-mens-sneakers"},
    { fileName: "size-chart-shoes.jpg", slug: "classic-canvas-sneakers"},
    { fileName: "size-chart-shoes.jpg", slug: "modern-high-top-sneakers"},
    { fileName: "size-chart-shoes.jpg", slug: "classic-leather-boots"},
    { fileName: "size-chart-shoes.jpg", slug: "stylish-chelsea-boots"},
    { fileName: "size-chart-shoes.jpg", slug: "outdoor-hiking-boots"},
    { fileName: "soze-chart-jumper.jpg", slug: "classic-elegant-wool-jumper"},
    { fileName: "soze-chart-jumper.jpg", slug: "casual-v-neck-modern-jumper"},
    { fileName: "soze-chart-jumper.jpg", slug: "sporty-zip-up-jumper"},
    // { fileName: "size-chart.jpg", slug: ""},
    // { fileName: "size-chart-shoes.jpg", slug: ""},
    // { fileName: "soze-chart-jumper.jpg", slug: ""},
    { fileName: "ties-1.jpg", slug: "classic-silk-tie"},
    { fileName: "ties-1-2.jpg", slug: "classic-silk-tie"},
    { fileName: "ties-1-3.jpg", slug: "classic-silk-tie"},
    { fileName: "ties-2.jpg", slug: "striped-business-tie"},
    { fileName: "ties-3.jpg", slug: "modern-skinny-tie"}
];

const objectsArray = []
productList.forEach((item) => {
    const fileName = item.fileName.replace(".jpg", "");
    const size = Math.floor(Math.random() * (350 - 250 + 1)) + 250;

    const isVariant = (n) => {
        const search = [
            'bright', 'green', 'red', 'blue', 'gray', 'black', 'peach', 'pink', 'orange', 'white'
        ]
        let result = false;
        search.forEach(i => {
            if (n.indexOf(i) !== -1) {
                result = true
            }
        })
        return result
    }

    if (isVariant(item.fileName)) {
        const type = "App\\Models\\ProductVariant"
        item.variants.forEach(option => {
            objectsArray.push({
                url: "/sample-store-data/sample-store-1/product-images/"+item.fileName,
                file_name: item.fileName,
                fileable_type: type,
                fileable_id: item.slug+option,
                title: "",
                description: "",
                size,
                type: 1
            })
        })
    } else {
        const type =  "App\\Models\\Product"
        objectsArray.push({
            url: "/sample-store-data/sample-store-1/product-images/"+item.fileName,
            file_name: item.fileName,
            fileable_type: type,
            fileable_id: item.slug,
            title: "",
            description: "",
            size,
            type: 1
        })
    }

});

console.log(JSON.stringify(objectsArray));

