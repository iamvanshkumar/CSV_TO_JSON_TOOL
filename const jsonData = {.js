awardeeDetail: [
    {
        activityType: entry["awardeeDetail/activityType"],
        affiliationOf: [{
            awardeePersonId: entry["awardeeDetail/affiliationOf/awardeePersonId"],
            emailAddress: entry["awardeeDetail/affiliationOf/emailAddress"],
            familyName: entry["awardeeDetail/affiliationOf/familyName"],
            fundingBodyPersonId: entry["awardeeDetail/affiliationOf/fundingBodyPersonId"],
            givenName: entry["awardeeDetail/affiliationOf/givenName"],
            identifier: [{
                type: "ORCID",
                value: entry["awardeeDetail/affiliationOf/identifier/id/orcid"]
            }],
            initials: entry["awardeeDetail/affiliationOf/initials"],
            name: [{
                language: "en",
                value: entry["awardeeDetail/affiliationOf/name"],
            }],
            role: entry["awardeeDetail/affiliationOf/role"],
            sourceRole: "NOT FOUND"
        }],
        awardeeAffiliationId: entry["awardeeDetail/awardeeAffiliationId"],
        departmentName: [{
            language: "en",
            value: entry["awardeeDetail/departmentName"],
            hasProvenance: {
                createdOn: new Date().toISOString().split("T")[0] + "T00:00:00",
                createdBy: "MPS",
                createdWith: "MPS SERVICES"
            }
        }],
        fundingBodyOrganizationId: entry["awardeeDetail/fundingBodyOrganizationId"],
        fundingTotal: [{
            amount: entry["awardeeDetail/fundingTotal/amount"],
            currency: entry["fundingDetail/fundingTotal/currency"]
        }],
        hasPostalAddress: {
            addressCountry: entry["awardee/country"],
            addressLocality: entry["awardee/addressLocality"],
            addressPostalCode: entry["awardee/addressPostalCode"],
            addressRegion: entry["awardee/addressRegion"],
            postOfficeBoxNumber: entry["awardee/postOfficeBoxNumber"],
            streetAddress: entry["awardee/streetAddress"]
        },
        identifier: [{
            type: "ROR",
            value: entry["awardeeDetail/identifier"]
        }],
        link: "NOT FOUND",
        name: [{
            language: "en",
            value: entry["awardeeDetail/name"]
        }],
        role: entry["awardeeDetail/role"],
        sourceRole: "NOT FOUND",
        vatNumber: entry["awardeeDetail/vatNumber"]
    }
],