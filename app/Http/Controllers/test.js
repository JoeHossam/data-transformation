const claims = [
    {
        id: 1,
        reference: "CL-23-3",
        payer_id: 1,
        authorization_notes: "hello world",
        internal_notes: "foo",
    },
];

const payers = [
    {
        id: 1,
        name: "Ahmed",
        phone: "+20213251",
    },
];

const claim_status = [
    {
        id: 1,
        claim_id: 1,
        status: "pending",
        date: "2024-10-31",
    },
    {
        id: 2,
        claim_id: 1,
        status: "approved",
        date: "2024-11-01",
    },
    {
        id: 3,
        claim_id: 1,
        status: "completed",
        date: "2024-11-05",
    },
];

const mapping_rules = [
    {
        id: 1,
        internal_field: "reference",
        external_field: "claimReference",
        data_type: "attribute",
        parent_id: null,
        endpoint_id: 22,
    },
    {
        id: 2,
        internal_field: null,
        external_field: "payer",
        data_type: "object",
        parent_id: null,
        endpoint_id: 22,
    },
    {
        id: 3,
        internal_field: "payer.name",
        external_field: "payerName",
        data_type: "attribute",
        parent_id: 2,
        endpoint_id: 22,
    },
    {
        id: 4,
        internal_field: "payer.phone",
        external_field: "payerPhone",
        data_type: "attribute",
        parent_id: 2,
        endpoint_id: 22,
    },
    {
        id: 5,
        internal_field: null,
        external_field: "notes",
        data_type: "array",
        parent_id: null,
        endpoint_id: 22,
    },
    {
        id: 6,
        internal_field: "authorization_notes",
        external_field: "authorizationNotes",
        data_type: "attribute",
        parent_id: 5,
        endpoint_id: 22,
    },
    {
        id: 7,
        internal_field: "internal_notes",
        external_field: "internalNotes",
        data_type: "attribute",
        parent_id: 5,
        endpoint_id: 22,
    },
    {
        id: 8,
        internal_field: "statuses",
        external_field: "claimStatuses",
        data_type: "object_list",
        parent_id: null,
        endpoint_id: 22,
    },
    {
        id: 9,
        internal_field: "date",
        external_field: "date",
        data_type: "attribute",
        parent_id: 8,
        endpoint_id: 22,
    },
    {
        id: 10,
        internal_field: "status",
        external_field: "status",
        data_type: "attribute",
        parent_id: 8,
        endpoint_id: 22,
    },
];

/**
 * Assumptions:
 * 1. there are no more than 1 level of nesting
 * 2. `ObjectList` dataType will always refer to a table
 * 3. children of `ObjectList` dataType are the columns to be included from the table
 * @param {*} claim_id the claim in question
 * @param {*} endpoint_id the endpoint schema to generate
 * @returns the generated schema
 */

const process = (claim_id, endpoint_id) => {
    // get schema
    const schema = mapping_rules.filter(
        (rule) => rule.endpoint_id === endpoint_id
    );

    if (!schema) return "no schema found";

    schema.sort((a) => a === null);

    const finalBody = {};

    /**
     * @typedef {Object} KeyDetails
     * @property {string} keyName - The name of the key.
     * @property {string} type - The type associated with the key.
     */

    /**
     * @type {Object<number, KeyDetails>}
     */
    const keyMap = {};

    for (let i = 0; i < schema.length; i++) {
        const rule = schema[i];

        if (rule.parent_id === null) {
            // root element
            if (rule.data_type === "attribute") {
                finalBody[rule.external_field] = getValueOfInternalField(
                    rule.internal_field,
                    claim_id
                );
            } else if (rule.data_type === "object") {
                finalBody[rule.external_field] = {};
            } else if (rule.data_type === "array") {
                finalBody[rule.external_field] = [];
            } else {
                // case rule.data_type === "object_list"
                const childrenFieldsInCollection = schema
                    .filter((r) => r.parent_id === rule.id)
                    .map((r) => r.internal_field);

                finalBody[rule.external_field] = getCollection(
                    rule.internal_field,
                    claim_id,
                    childrenFieldsInCollection
                );

                continue;
            }

            keyMap[rule.id] = {
                keyName: rule.external_field,
                type: rule.data_type,
            };

            continue;
        }

        const { keyName, type } = keyMap[rule.parent_id];

        if (type === "array") {
            finalBody[keyName].push(
                getValueOfInternalField(rule.internal_field, claim_id)
            );
        } else if (type === "object") {
            finalBody[keyName] = {
                ...finalBody[keyName],
                [rule.external_field]: getValueOfInternalField(
                    rule.internal_field,
                    claim_id
                ),
            };
        }
    }

    return finalBody;
};

/**
 *
 * @param {string} fieldName
 * @param {string} claim_id
 */
const getValueOfInternalField = (fieldName, claim_id) => {
    if (!fieldName.includes(".")) {
        const claim = claims.find((claim) => claim.id === claim_id);
        if (claim[fieldName]) return claim[fieldName];
    } else {
        const [table, field] = fieldName.split(".");
        if (table === "payer") {
            const claim = claims.find((claim) => claim.id === claim_id);
            const row = payers.find((payer) => payer.id === claim.payer_id);

            if (!row) {
                console.error(`no payer found with id ${claim.payer_id}`);
                return null;
            }
            return row[field];
        }
        if (table === "claim_status") {
            const row = claim_status.find((item) => item.claim_id === claim_id);

            if (!row) {
                console.error(`no claim_status found for ${claim_id}`);
                return null;
            }
            if (row[field]) return row[field];
        }
    }

    console.error("field not found");
    return null;
};

/**
 *
 * @param {string} tableName
 * @param {string} claimId
 * @param {string[]} cols
 */
const getCollection = (tableName, claimId, cols) => {
    return claim_status
        .filter((status) => status.claim_id === claimId)
        .map((status) => {
            return Object.keys(status)
                .filter((key) => cols.includes(key))
                .reduce((acc, curr) => ({ ...acc, [curr]: status[curr] }), {});
        });
};

console.log(process(1, 22));
