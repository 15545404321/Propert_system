Vue.component('Detail', {
	template: `
		<el-dialog title="查看详情" width="1000px" class="icon-dialog" :visible.sync="show" @open="open" :before-close="closeForm" append-to-body>
			<table cellpadding="0" cellspacing="0" class="table table-bordered" align="center" width="100%" style="word-break:break-all; margin-bottom:30px;  font-size:13px;">
				<tbody>
					<tr>
						<td class="title" width="100">客户名称：</td>
						<td>
							{{form.member_name}}
						</td>
					</tr>
					<tr>
						<td class="title" width="100">客户手机：</td>
						<td>
							{{form.member_tel}}
						</td>
					</tr>
					<tr>
						<td class="title" width="100">客户生日：</td>
						<td>
							{{parseTime(form.member_birthday,'{y}-{m}-{d}')}}
						</td>
					</tr>
					<tr>
						<td class="title" width="100">客户性别：</td>
						<td>
							<span v-if="form.member_sex == '1'">男</span>
							<span v-if="form.member_sex == '2'">女</span>
						</td>
					</tr>
					<tr>
						<td class="title" width="100">住卡数量：</td>
						<td>
							{{form.member_hksl}}
						</td>
					</tr>
					<tr>
						<td class="title" width="100">客户职业：</td>
						<td>
							{{form.member_khzy}}
						</td>
					</tr>
					<tr>
						<td class="title" width="100">户口地址：</td>
						<td>
							{{form.member_hkdz}}
						</td>
					</tr>
					<tr>
						<td class="title" width="100">工作单位：</td>
						<td>
							{{form.member_gzdw}}
						</td>
					</tr>
					<tr>
						<td class="title" width="100">公司简介：</td>
						<td>
							{{form.member_gsjj}}
						</td>
					</tr>
					<tr>
						<td class="title" width="100">备注信息：</td>
						<td>
							{{form.member_remark}}
						</td>
					</tr>
					<tr>
						<td class="title" width="100">房源确认：</td>
						<td>
							{{parseTime(form.member_fyqrrq,'{y}-{m}-{d}')}}
						</td>
					</tr>
					<tr>
						<td class="title" width="100">入户通知：</td>
						<td>
							{{parseTime(form.member_rhtzrq,'{y}-{m}-{d}')}}
						</td>
					</tr>
					<tr>
						<td class="title" width="100">业主照片：</td>
						<td>
							<el-image v-if="form.member_yzzp" class="table_list_pic" :src="form.member_yzzp"  :preview-src-list="[form.member_yzzp]"></el-image>
						</td>
					</tr>
					<tr>
						<td class="title" width="100">证件照片：</td>
						<td>
							<el-image v-if="form.member_zjzp" class="table_list_pic" :src="form.member_zjzp"  :preview-src-list="[form.member_zjzp]"></el-image>
						</td>
					</tr>
				</tbody>
			</table>
			<table cellpadding="0" cellspacing="0" class="table table-bordered" align="center" width="100%" style="word-break:break-all; margin-bottom:30px;  font-size:13px;">
				<tbody>
					<tr>
						<td class="title" width="100">家庭成员：</td>
						<td>
							{{form.member_idx}}
						</td>
					</tr>
					<tr>
						<td class="title" width="100">名下住房：</td>
						<td>
							{{form.fcxx_fjbhx}}
						</td>
					</tr>
					<tr>
						<td class="title" width="100">名下门市：</td>
						<td>
							{{form.fcxx_ms}}
						</td>
					</tr>
					<tr>
						<td class="title" width="100">名下车库：</td>
						<td>
							{{form.fcxx_ck}}
						</td>
					</tr>
					<tr>
						<td class="title" width="100">名下车辆：</td>
						<td>
							{{form.car_namex}}
						</td>
					</tr>
					<tr>
						<td class="title" width="100">名下车位：</td>
						<td>
							{{form.cewei_namex}}
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
			axios.post(base_url+'/Member/detail',this.info).then(res => {
				this.form = res.data.data
			})
		},
		closeForm(){
			this.$emit('update:show', false)
		}
	}
})
