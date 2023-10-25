const productId = "classic-canvas-sneakers"
const sku = "M-SN002"
const price = 22.5
const attributeOptions = [
    // [
    //     "white",
    //     "brightblue",
    //     "brightgreen",
    //     "brightorange",
    // ],
    // [
    //     "8",
    //     "9",
    //     "10",
    // ],
    [
        "8",
        "8.5",
        "9",
        "9.5",
        "10",
    ],
    // [
    //     "xs",
    //     "s",
    //     "m",
    //     "l",
    //     "xl",
    // ]
]

function getRandomInt(min, max) {
    min = Math.ceil(min);
    max = Math.floor(max);
    return Math.floor(Math.random() * (max - min) + min); // The maximum is exclusive and the minimum is inclusive
}

let combinations = [];
const getVariant = (o, index) => {
    attributeOptions.forEach((element, elementIndex) => {
        if (index !== elementIndex) {
            combinations.push(o+"-"+element)
        }
    })
}

const getCombinationDataset = (el1, el2 = null) => {
    if (el2) {
        return {
            slug: productId+"-"+el1+"-"+el2,
            price: price,
            product_id: productId,
            data: "",
            stock: getRandomInt(-1, 54),
            sku: sku+'-'+el1.toUpperCase()+'-'+el2.toUpperCase(),
            description: "",
            attribute_options: ["option-"+el1+"-1", "option-"+el2+"-1"]
        }
    } else {
        return {
            slug: productId+"-"+el1,
            price: price,
            product_id: productId,
            data: "",
            stock: getRandomInt(-1, 54),
            sku: sku+'-'+el1.toUpperCase(),
            description: "",
            attribute_options: ["option-"+el1+"-1"]
        }
    }
}
const getCombinations = () => {
    if (attributeOptions.length === 2) {
        attributeOptions[0].forEach((el1) => {
            attributeOptions[1].forEach((el2) => {
                combinations.push(getCombinationDataset(el1, el2))
            })
        })
    } else {
        attributeOptions[0].forEach((el1) => {
            combinations.push(getCombinationDataset(el1))
        })
    }
}

const getVariants = (attributeOptions) => {
        getCombinations()
    // attributeOptions.map(getVariant)
    console.log(combinations)
}

getVariants(attributeOptions)

console.log(JSON.stringify(combinations))
