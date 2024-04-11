<template>
	<f7-page :name="name" class="display-flex flex-direction-column align-items-center" :page-content="false" @page:beforein="On" @page:afterout="Off">
		<iframe v-if="on" :src="src" :class="rotate" allowfullscreen></iframe>
	</f7-page>
</template>
<script>
export default {
	props: {
		f7route: Object,
		f7router: Object,
	},
	inject:["token"],
	data(){
		const{name,route:{options}}=this.f7route;

		return{
			name,
			rotate:options.rotate ? "rotate"+options.rotate : {},
			src:options.host+name+"?"+this.token,
			on:false
		};
	},
	methods:{
		On(){
			this.$root.active=this.name;
			this.on=true;
		},

		Off(){
			this.on=false;
		},
	}
};
</script>