Vue.component('Detail', {
	template: `
		<el-dialog title="查看详情" width="600px" class="icon-dialog" :visible.sync="show" @open="open" :before-close="closeForm" append-to-body>
			<table cellpadding="0" cellspacing="0" class="table table-bordered" align="center" width="100%" style="word-break:break-all; margin-bottom:30px;  font-size:13px;">
				<tbody>
					<tr>
						<td class="title" width="100">广告图片：</td>
						<td>
							<el-image v-if="form.admanage_pic" class="table_list_pic" :src="form.admanage_pic"  :preview-src-list="[form.admanage_pic]"></el-image>
						</td>
					</tr>
					<tr>
						<td class="title" width="100">所属页面：</td>
						<td>
							<span v-if="form.admanage_page == '1'">服务月报</span>
							<span v-if="form.admanage_page == '2'">申请报修</span>
							<span v-if="form.admanage_page == '3'">便民电话</span>
							<span v-if="form.admanage_page == '4'">我的中心</span>
						</td>
					</tr>
					<tr>
						<td class="title" width="100">页面位置：</td>
						<td>
							<span v-if="form.admanage_position == '1'">顶图</span>
							<span v-if="form.admanage_position == '2'">中图</span>
						</td>
					</tr>
					<tr>
						<td class="title" width="100">所属物业：</td>
						<td>
							{{form.shop_id}}
						</td>
					</tr>
				</tbody>
			</table>
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
			form:{
			},
		}
	},
	methods: {
		open(){
			axios.post(base_url+'/AdManage/detail',this.info).then(res => {
				this.form = res.data.data
			})
		},
		closeForm(){
			this.$emit('update:show', false)
		}
	}
})
