<template>
    <modal
        :title = "'Add a new ' + utmType.type"
        :submit-text = "'Add ' + utmType.type"
        size="small"
        :show.sync="open"
        :submit-loading="btnLoading"
        @submit="createUtm"
        @close="$emit('update:open', false)">

        <form data-vv-scope="form-add-utm" class="mt-3">

            <div class="form-group">
                <label>Name</label>
                <input type="text" name="name" placeholder="e.g. summer_sale"
                    :class="['form-control', { 'is-invalid': errors.has('form-add-utm.name') }]"
                    v-validate="'required|max:40'"
                    v-model="newUtm.name">
                <div class="invalid-feedback" v-show="errors.has('form-add-utm.name')">{{ errors.first('form-add-utm.name') }}</div>
            </div>

            <div class="form-group">
                <label>Description</label>
                <textarea rows="5" name="description" placeholder="e.g. summer_sale"
                    :class="['form-control', { 'is-invalid': errors.has('form-add-utm.description') }]"
                    v-validate="'required'"
                    v-model="newUtm.description"></textarea>
                <div class="invalid-feedback" v-show="errors.has('form-add-utm.description')">{{ errors.first('form-add-utm.description') }}</div>
            </div>

            <div class="custom-control custom-switch">
                <input type="checkbox" class="custom-control-input" id="disable_utm" name="disable_utm" :true-value="true" v-model="newUtm.disabled">
                <label class="custom-control-label" for="disable_utm">Active</label>
            </div>

        </form>

    </modal>
</template>


<script>
export default {
	name: 'add-utm-modal',

    props: {
        open: {
            type: Boolean,
            twoWay: true,
            default: false
        },

        utmType: {
            index: Number,
            name: String
        }
    },


    data() {
        return {
            btnLoading: false,

            newUtm: {
                utmType: Number,
                disabled: true
            },
        };
    },

    methods: {
        createUtm() {
            this.$validator.validateAll('form-add-utm').then((success) => {
                if ( ! success )
                    return

                this.btnLoading = true;

                let tempUtm = Object.assign({}, this.newUtm);
                tempUtm.utmType = this.utmType.index;
                tempUtm.disabled = this.newUtm.disabled ? null : 1 ;

                axios.post('utms', tempUtm)
                .then(response => {

                    this.btnLoading = false;
                    this.$emit('update:open', false);

                    this.$resetForm(this.newUtm);
                    this.$alert.success(response.data.message);

                    // Emit a done event so that the parent can take action
                    this.$emit('done');

                })
                .catch(error => {

                    this.btnLoading = false;
                    this.$backendErrors(error.response.data, 'form-add-utm');

                    // Show error message if there is any
                    if ( error.response.data.message )
                        this.$alert.error(error.response.data.message);

                });

            });
        }
    }

}
</script>
