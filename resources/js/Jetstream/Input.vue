<template>
	 <div class="col-span-6 sm:col-span-4 mb-5">
    	<input class="form-input rounded-md shadow-sm" :value="value" @input="$emit('input', $event.target.value)" ref="input">
 	 </div>
</template>

<script>
    export default {
        props: ['value'],

        methods: {
            focus() {
                this.$refs.input.focus()
            }
        }
    }
</script>

