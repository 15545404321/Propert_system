Vue.component('Detail', {
	template: `
		<el-dialog title="查看详情" width="600px" class="icon-dialog" :visible.sync="show" @open="open" :before-close="closeForm" append-to-body>
			<table cellpadding="0" cellspacing="0" class="table table-bordered" align="center" width="100%" style="word-break:break-all; margin-bottom:30px;  font-size:13px;">
				<tbody>
					<tr>
						<td class="title" width="100">商家名称：</td>
						<td>
							{{form.bmdh_title}}
						</td>
					</tr>
					<tr>
						<td class="title" width="100">商家介绍：</td>
						<td>
							{{form.bmdh_neirong}}
						</td>
					</tr>
					<tr>
						<td class="title" width="100">电话号码：</td>
						<td>
							{{form.bmdh_tel}}
						</td>
					</tr>
					<tr>
						<td class="title" width="100">添加时间：</td>
						<td>
							{{parseTime(form.bmdh_date,'{y}-{m}-{d}')}}
						</td>
					</tr>
					<tr>
						<td class="title" width="100">到期时间：</td>
						<td>
							{{parseTime(form.hmdh_end,'{y}-{m}-{d}')}}
						</td>
					</tr>
					<tr>
						<td class="title" width="100">电话分类：</td>
						<td>
							{{form.dhfl_id}}
						</td>
					</tr>
					<tr>
						<td class="title" width="100">联系人员：</td>
						<td>
							{{form.bmdh_lxr}}
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
			axios.post(base_url+'/Bmdh/detail',this.info).then(res => {
				this.form = res.data.data
			})
		},
		closeForm(){
			this.$emit('update:show', false)
		}
	}
})
