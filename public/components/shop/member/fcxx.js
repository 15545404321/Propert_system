Vue.component('Fcxx', {
	template: `
		<el-drawer title="客户资产"  direction="rtl" size="1550px" class="icon-dialog" :visible.sync="show" @open="open" :before-close="closeForm" append-to-body>
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
			Object.assign(query, {member_id:this.info.member_id})
			this.jump = '/shop/fcxx/index?' + param(query)
		},
		closeForm(){
			this.$emit('update:show', false)
		}
	}
})
