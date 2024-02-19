Vue.component('Fpchewei', {
	template: `
		<el-drawer title="分配车位"  direction="rtl" size="1450px" class="icon-dialog" :visible.sync="show" @open="open" :before-close="closeForm" append-to-body>
			<div style="margin:0 30px;">
			<iframe :src="jump" frameborder="0" width="100%" height="800px"></iframe>
			</div>
		</el-drawer>
	`
	,
	props: {
		show: {
			type: Boolean,
			default: true
		},
		size: {
			type: String,
			default: 'mini'
		},
		info: {
			type: Object,
		},
	},
	data() {
		return {
			jump:''
		}
	},
	methods: {
		open(){
			let query = {}
			Object.assign(query, {dialogstate:1})
			Object.assign(query, {fybz_id:this.info.fybz_id})
			Object.assign(query, {fydy_id:this.info.fydy_id})
			Object.assign(query, {fylx_id:this.info.fylx_id})
			this.jump = '/shop/Fyfp/index?' + param(query)
		},
		closeForm(){
			this.$emit('update:show', false)
		}
	}
})
