const productId = "casual-crew-neck-t-shirt"
const sku = "M-T003"
const price = 14.99
const attributeOptions = [
    [
        "white",
        "brightblue",
        "brightgreen",
        "brightorange",
    ],
    [
        "xs",
        "s",
        "m",
        "l",
        "xl",
    ]
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
    attributeOptions[0].forEach((el1) => {
            attributeOptions[1].forEach((el2) => {
                combinations.push(getCombinationDataset(el1, el2))
            })
    }
    )
}

const getVariants = (attributeOptions) => {
    if (attributeOptions.length === 2) {
        getCombinations()
    } else {
        combinations = [...attributeOptions[0]]
    }
    // attributeOptions.map(getVariant)
    console.log(combinations)
}

getVariants(attributeOptions)

console.log(JSON.stringify(combinations))
