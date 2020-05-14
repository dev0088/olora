<template>
	<form @submit.prevent="createLink" class="w-100" data-vv-scope="form_add_link">

	   <div class="input-group">

			<input type="text" name="url"
                placeholder="Paste your destination URL"
                :class="[{ 'is-invalid': errors.has('form_add_link.url') }, 'form-control']"
                v-validate="{required: true, url: 'require_protocol'}" 
				v-model="newLink.url">

			<div class="input-group-append">
				<button-spinner :loading="btnLoadingAddLink" class="btn-primary">Add Link</button-spinner>
			</div>

		</div>
        <div class="invalid-feedback" v-show="errors.has('form_add_link.url')">{{ errors.first('form_add_link.url') }}</div>

	</form>
</template>


<script>
export default {
    data() {
        return {
            btnLoadingAddLink: false,

            utms: [
                {
                    index: 0,
                    type: 'campaign'
                },
                {
                    index: 1,
                    type: 'medium'
                },
                {
                    index: 2,
                    type: 'source'
                },
                {
                    index: 3,
                    type: 'content'
                },
                {
                    index: 4,
                    type: 'term'
                }
            ],

            newLink: {}
        };
    },

    methods: {
        createLink() {
            this.$validator.validateAll('form_add_link').then((success) => {
                
                // Return if validation failed
                if ( ! success )
                    return
                
                let tempLink = this.newLink;
                tempLink.url = this.filterURL(tempLink.url);
                this.btnLoadingAddLink = true;

                axios.post('links', tempLink)
                .then(response => {
                    
                    this.btnLoadingAddLink = false;
                    this.$alert.success('Your link was successfully added');

                    // Redirect to link builder
                    this.$router.push('/links/creator/' + response.data.id);

                })
                .catch(error => {

                    this.btnLoadingAddLink = false;

                    this.$backendErrors(error.response.data, 'form_add_link');

                    // Show error message if there is any
                    if ( error.response.data.message )
                        this.$alert.error(error.response.data.message);
                    
                });

            });
        },

        filterURL(url) {
            const params = new URL(url);
            const searchs = params.search.replace('?','').split('&');
            const filteredSearchs = searchs.filter(query => {
                const includeUtm = this.utms.filter(utm => {
                    if ( query.includes(`utm_${utm.type}`) )
                        return utm;
                });

                if ( includeUtm.length == 0 )
                    return query;
            });

            let originPathURL = '';
            if ( filteredSearchs.length == 0 )
                originPathURL = params.origin + params.pathname;
            else {
                originPathURL = params.origin + params.pathname + '?';
                filteredSearchs.map((search, index) => {
                    originPathURL = originPathURL + search;
                    if ( index !== filteredSearchs.length - 1 )
                        originPathURL += '&';
                })
            }

            return originPathURL;
        }
    }
}
</script>