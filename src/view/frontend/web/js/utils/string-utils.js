/*
 * Copyright Extend (c) 2023. All rights reserved.
 * See Extend-COPYING.txt for license details.
 */
/* const stringUtils = function (){*/

const stringUtils = {
    sanitizeForElementId: (str) => {
        return str.replace(/[^a-zA-Z0-9_|]/g, '')
    }
}

export {stringUtils as default};

