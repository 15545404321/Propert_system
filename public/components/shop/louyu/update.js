Vue.component('Update', {
	template: `
		<div v-if="show">
			<el-form :size="size" ref="form" :model="form" :rules="rules" :label-width="'90px'">
			<el-tabs v-model="activeName">
				<el-tab-pane style="padding-top:10px"  label="基础信息" name="基础信息">
				<el-row >
					<el-col :span="24">
						<el-form-item label="楼宇名称" prop="louyu_name">
							<el-input  v-model="form.louyu_name" autoComplete="off" clearable  placeholder="请输入楼宇名称"></el-input>
						</el-form-item>
					</el-col>
				</el-row>
				<el-row >
					<el-col :span="24">
						<el-form-item label="楼宇类型" prop="louyutype_id">
							<el-select   style="width:100%" v-model="form.louyutype_id" filterable clearable placeholder="请选择楼宇类型">
								<el-option v-for="(item,i) in louyutype_ids" :key="i" :label="item.key" :value="item.val"></el-option>
							</el-select>
						</el-form-item>
					</el-col>
				</el-row>
				<el-row >
					<el-col :span="24">
						<el-form-item label="楼房属性" prop="louyusx_id">
							<el-select   style="width:100%" v-model="form.louyusx_id" filterable clearable placeholder="请选择楼房属性">
								<el-option v-for="(item,i) in louyusx_ids" :key="i" :label="item.key" :value="item.val"></el-option>
							</el-select>
						</el-form-item>
					</el-col>
				</el-row>
				<el-row >
					<el-col :span="24">
						<el-form-item label="建筑时间" prop="louyu_jzsj">
							<el-date-picker value-format="yyyy-MM-dd" type="date" v-model="form.louyu_jzsj" clearable placeholder="请输入建筑时间"></el-date-picker>
						</el-form-item>
					</el-col>
				</el-row>
				<el-row >
					<el-col :span="24">
						<el-form-item label="建筑面积" prop="louyu_jzmj">
							<el-input-number controls-position="right" style="width:200px;" autoComplete="off" v-model="form.louyu_jzmj" clearable :min="0" placeholder="请输入建筑面积"/>
						</el-form-item>
					</el-col>
				</el-row>
				<el-row >
					<el-col :span="24">
						<el-form-item label="建设单位" prop="louyu_jsdw">
							<el-input  v-model="form.louyu_jsdw" autoComplete="off" clearable  placeholder="请输入建设单位"></el-input>
						</el-form-item>
					</el-col>
				</el-row>
				<el-row >
					<el-col :span="24">
						<el-form-item label="电梯数量" prop="louyu_dtsl">
							<el-input-number controls-position="right" style="width:200px;" autoComplete="off" v-model="form.louyu_dtsl" clearable :min="0" placeholder="请输入电梯数量"/>
						</el-form-item>
					</el-col>
				</el-row>
				</el-tab-pane>
			</el-tabs>
				<el-form-item>
					<el-button :size="size" type="primary" @click="submit">保存设置</el-button>
					<el-button :size="size" icon="el-icon-back" @click="closeForm">返回</el-button>
				</el-form-item>
			</el-form>
		</div>
	`
	,
	components:{
	},
	props: {
		show: {
			type: Boolean,
			default: false
		},
		size: {
			type: String,
			default: 'small'
		},
		info: {
			type: Object,
		},
	},
	data(){
		return {
			form: {
				shop_id:'',
				xqgl_id:'',
				louyu_name:'',
				louyutype_id:1,
				louyusx_id:1,
				louyu_flcs:0,
				louyu_jzsj:'',
				louyu_jzmj:1.00,
				louyu_jsdw:'',
				louyu_dtsl:0,
				louyu_dscs:0,
				louyu_ycjh:0,
			},
			louyu_pids:[],
			louyutype_ids:[],
			louyusx_ids:[],
			loading:false,
			activeName:'基础信息',
			rules: {
				louyu_name:[
					{required: true, message: '楼宇名称不能为空', trigger: 'blur'},
				],
				louyutype_id:[
					{required: true, message: '楼宇类型不能为空', trigger: 'change'},
				],
				louyusx_id:[
					{required: true, message: '楼房属性不能为空', trigger: 'change'},
				],
				louyu_dysl:[
					{required: true, message: '单元数量不能为空', trigger: 'blur'},
					{pattern:/^[0-9]*$/, message: '单元数量格式错误'}
				],
				louyu_lczs:[
					{required: true, message: '楼层总数不能为空', trigger: 'blur'},
					{pattern:/^[0-9]*$/, message: '楼层总数格式错误'}
				],
				louyu_chzs:[
					{required: true, message: '层户总数不能为空', trigger: 'blur'},
					{pattern:/^[0-9]*$/, message: '层户总数格式错误'}
				],
				louyu_flcs:[
					{required: true, message: '负楼层数不能为空', trigger: 'blur'},
					{pattern:/^[0-9]*$/, message: '负楼层数格式错误'}
				],
				louyu_jzmj:[
					{pattern:/(^[1-9]([0-9]+)?(\.[0-9]{1,2})?$)|(^(0){1}$)|(^[0-9]\.[0-9]([0-9])?$)/, message: '建筑面积格式错误'}
				],
				louyu_dtsl:[
					{pattern:/^[0-9]*$/, message: '电梯数量格式错误'}
				],
				louyu_dscs:[
					{required: true, message: '底商层数不能为空', trigger: 'blur'},
					{pattern:/^[0-9]*$/, message: '底商层数格式错误'}
				],
				louyu_ycjh:[
					{required: true, message: '一层几户不能为空', trigger: 'blur'},
					{pattern:/^[0-9]*$/, message: '一层几户格式错误'}
				],
			}
		}
	},
	watch:{
		show(val){
			if(val){
				axios.post(base_url + '/Louyu/getFieldList').then(res => {
					if(res.data.status == 200){
						this.louyu_pids = res.data.data.louyu_pids
						this.louyutype_ids = res.data.data.louyutype_ids
						this.louyusx_ids = res.data.data.louyusx_ids
					}
				})
			}
			if(val){
				this.open()
			}
		}
	},
	methods: {
		open(){
			this.form = this.info
			this.form.louyu_jzsj = parseTime(this.form.louyu_jzsj)
		},
		submit(){
			this.$refs['form'].validate(valid => {
				if(valid) {
					this.loading = true
					axios.post(base_url + '/Louyu/update',this.form).then(res => {
						if(res.data.status == 200){
							this.$message({message: res.data.msg, type: 'success'})
							this.$emit('refesh_list')
							this.closeForm()
						}else{
							this.loading = false
							this.$message.error(res.data.msg)
						}
					}).catch(()=>{
						this.loading = false
					})
				}
			})
		},
		closeForm(){
			this.$emit('update:show', false)
			this.loading = false
			if (this.$refs['form']!==undefined) {
				this.$refs['form'].resetFields()
			}
			this.$emit('changepage')
		},
	}
})
