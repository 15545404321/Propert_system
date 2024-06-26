Vue.component('Wxcaidan', {
	template: `
		<el-drawer title="公众号菜单导航"  direction="rtl" size="1420px" class="icon-dialog" :visible.sync="show" @open="open" :before-close="closeForm" append-to-body>
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
			Object.assign(query, {shop_id:this.info.shop_id})
			this.jump = '/admin/ShopWxConfig/index?' + param(query)
		},
		closeForm(){
			this.$emit('update:show', false)
		}
	}
})
