Vue.component('Ryxxsz', {
	template: `
		<el-dialog title="扩展信息" width="1200px" class="icon-dialog" :visible.sync="show" @open="open" :before-close="closeForm" append-to-body>
			<iframe :src="jump" frameborder="0" width="100%" height="600px"></iframe>
		</el-dialog>
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
			Object.assign(query, {shop_admin_id:this.info.shop_admin_id})
			Object.assign(query, {xqgl_id:this.info.xqgl_id})
			this.jump = '/shop/Ryxx/index?' + param(query)
		},
		closeForm(){
			this.$emit('update:show', false)
		}
	}
})
