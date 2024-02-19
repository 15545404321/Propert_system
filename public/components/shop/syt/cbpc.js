Vue.component('Cbpc', {
	template: `
		<el-drawer title="抄表管理"  direction="rtl" size="1420px" class="icon-dialog" :visible.sync="show" @open="open" :before-close="closeForm" append-to-body>
			<div style="margin:0 30px;">
			<iframe :src="jump" frameborder="0" width="100%" height="600px"></iframe>
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
			this.jump = '/shop/Cbpc/index?' + param(query)
		},
		closeForm(){
			this.$emit('update:show', false)
		}
	}
})
